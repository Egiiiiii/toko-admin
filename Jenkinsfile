pipeline {
    agent any

    environment {
        DOCKER_IMAGE = 'diwamln/toko-admin'
        DOCKER_CREDS = 'docker-hub'
        GIT_CREDS    = 'git-token'

        MANIFEST_REPO_URL  = 'github.com/DevopsNaratel/deployment-manifests'
        MANIFEST_TEST_PATH = 'toko-admin/dev/deployment.yaml'
        MANIFEST_PROD_PATH = 'toko-admin/prod/deployment.yaml'
    }

    stages {

        stage('Checkout & Versioning') {
            steps {
                checkout scm
                script {
                    def commitHash = sh(returnStdout: true, script: "git rev-parse --short HEAD").trim()
                    env.BASE_TAG = "build-${BUILD_NUMBER}-${commitHash}"
                    currentBuild.displayName = "#${BUILD_NUMBER} Backend (${env.BASE_TAG})"
                }
            }
        }

        stage('Debug Workspace') {
            steps {
                sh '''
                    echo "=== Branch ==="
                    git branch --show-current || true

                    echo "=== Workspace ==="
                    ls -lah
                '''
            }
        }

        stage('Build & Push (TEST Image)') {
            steps {
                script {
                    docker.withRegistry('', DOCKER_CREDS) {
                        def testTag = "${env.BASE_TAG}-test"

                        env.DOCKER_BUILDKIT = '1'
                        def buildArgs = "--build-arg BUILDKIT_INLINE_CACHE=1 --cache-from ${DOCKER_IMAGE}:latest"

                        sh "docker build ${buildArgs} -t ${DOCKER_IMAGE}:${testTag} ."
                        sh "docker push ${DOCKER_IMAGE}:${testTag}"
                    }
                }
            }
        }

        stage('Update Manifest (TEST)') {
            steps {
                script {
                    sh 'rm -rf temp_manifests'
                    dir('temp_manifests') {
                        withCredentials([usernamePassword(credentialsId: GIT_CREDS, usernameVariable: 'GIT_USER', passwordVariable: 'GIT_PASS')]) {

                            sh "git clone https://${GIT_USER}:${GIT_PASS}@${MANIFEST_REPO_URL} ."
                            sh 'git config user.email "jenkins@bot.com"'
                            sh 'git config user.name "Jenkins Pipeline"'

                            sh "sed -i 's|image: ${DOCKER_IMAGE}:.*|image: ${DOCKER_IMAGE}:${env.BASE_TAG}-test|g' ${MANIFEST_TEST_PATH}"

                            sh "git add ."
                            sh "git commit -m 'Deploy Backend TEST: ${env.BASE_TAG}-test [skip ci]'"
                            sh "git push origin main"
                        }
                    }
                }
            }
        }

        stage('Approval for Production') {
            steps {
                input message: "Backend TEST (${env.BASE_TAG}-test) sudah siap. Lanjutkan ke PROD?"
            }
        }

        stage('Build & Push (PROD Image)') {
            steps {
                script {
                    docker.withRegistry('', DOCKER_CREDS) {
                        def testImage = docker.image("${DOCKER_IMAGE}:${env.BASE_TAG}-test")
                        def prodTag = "${env.BASE_TAG}-prod"

                        testImage.pull()
                        testImage.push(prodTag)
                        testImage.push('latest')

                        echo "Image PROD siap: ${prodTag}"
                    }
                }
            }
        }

        stage('Update Manifest (PROD)') {
            steps {
                dir('temp_manifests') {
                    withCredentials([usernamePassword(credentialsId: GIT_CREDS, usernameVariable: 'GIT_USER', passwordVariable: 'GIT_PASS')]) {

                        sh "git pull origin main"

                        sh "sed -i 's|image: ${DOCKER_IMAGE}:.*|image: ${DOCKER_IMAGE}:${env.BASE_TAG}-prod|g' ${MANIFEST_PROD_PATH}"

                        sh "git add ."
                        sh "git commit -m 'Promote Backend PROD: ${env.BASE_TAG}-prod [skip ci]'"
                        sh "git push origin main"
                    }
                }
            }
        }
    }
}
