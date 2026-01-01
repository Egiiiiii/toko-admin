export const Ziggy = {
    url: "http://localhost",
    port: null,
    defaults: {},
    routes: {
        "filament.exports.download": { uri: "filament/exports/{export}/download", methods: ["GET","HEAD"], parameters:["export"], bindings: { export: "id" } },
        "filament.imports.failed-rows.download": { uri: "filament/imports/{import}/failed-rows/download", methods: ["GET","HEAD"], parameters:["import"], bindings: { import: "id" } },
        "filament.admin.auth.login": { uri: "admin/login", methods:["GET","HEAD"] },
        "filament.admin.auth.logout": { uri: "admin/logout", methods:["POST"] },
        "filament.admin.pages.dashboard": { uri: "admin", methods:["GET","HEAD"] },
        "filament.admin.resources.brands.index": { uri:"admin/brands", methods:["GET","HEAD"] },
        "filament.admin.resources.brands.create": { uri:"admin/brands/create", methods:["GET","HEAD"] },
        "filament.admin.resources.brands.edit": { uri:"admin/brands/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "filament.admin.resources.categories.index": { uri:"admin/categories", methods:["GET","HEAD"] },
        "filament.admin.resources.categories.create": { uri:"admin/categories/create", methods:["GET","HEAD"] },
        "filament.admin.resources.categories.edit": { uri:"admin/categories/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "filament.admin.resources.customers.index": { uri:"admin/customers", methods:["GET","HEAD"] },
        "filament.admin.resources.customers.create": { uri:"admin/customers/create", methods:["GET","HEAD"] },
        "filament.admin.resources.customers.edit": { uri:"admin/customers/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "filament.admin.resources.order-items.index": { uri:"admin/order-items", methods:["GET","HEAD"] },
        "filament.admin.resources.order-items.create": { uri:"admin/order-items/create", methods:["GET","HEAD"] },
        "filament.admin.resources.order-items.edit": { uri:"admin/order-items/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "filament.admin.resources.orders.index": { uri:"admin/orders", methods:["GET","HEAD"] },
        "filament.admin.resources.orders.create": { uri:"admin/orders/create", methods:["GET","HEAD"] },
        "filament.admin.resources.orders.edit": { uri:"admin/orders/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "filament.admin.resources.payments.index": { uri:"admin/payments", methods:["GET","HEAD"] },
        "filament.admin.resources.payments.create": { uri:"admin/payments/create", methods:["GET","HEAD"] },
        "filament.admin.resources.payments.edit": { uri:"admin/payments/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "filament.admin.resources.products.index": { uri:"admin/products", methods:["GET","HEAD"] },
        "filament.admin.resources.products.create": { uri:"admin/products/create", methods:["GET","HEAD"] },
        "filament.admin.resources.products.edit": { uri:"admin/products/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "filament.admin.resources.reviews.index": { uri:"admin/reviews", methods:["GET","HEAD"] },
        "filament.admin.resources.reviews.create": { uri:"admin/reviews/create", methods:["GET","HEAD"] },
        "filament.admin.resources.reviews.edit": { uri:"admin/reviews/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "filament.admin.resources.users.index": { uri:"admin/users", methods:["GET","HEAD"] },
        "filament.admin.resources.users.create": { uri:"admin/users/create", methods:["GET","HEAD"] },
        "filament.admin.resources.users.edit": { uri:"admin/users/{record}/edit", methods:["GET","HEAD"], parameters:["record"] },
        "livewire.update": { uri:"livewire/update", methods:["POST"] },
        "livewire.upload-file": { uri:"livewire/upload-file", methods:["POST"] },
        "livewire.preview-file": { uri:"livewire/preview-file/{filename}", methods:["GET","HEAD"], parameters:["filename"] },
        "home": { uri:"/", methods:["GET","HEAD"] },
        "product.show": { uri:"product/{product}", methods:["GET","HEAD"], parameters:["product"], bindings:{ product:"id" } },
        "storage.local": { uri:"storage/{path}", methods:["GET","HEAD"], wheres:{ path:".*" }, parameters:["path"] }
    }
};

// Merge dengan window.Ziggy jika ada
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}
