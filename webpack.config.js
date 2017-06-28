module.exports = [
    {
        entry: {
            "settings": "./app/views/admin/settings.js",
            "link": "./app/components/link.vue",
            "item-index": "./app/views/admin/item-index",
            "item-edit": "./app/views/admin/item-edit",
            "item-meta": "./app/components/item-meta.vue",
            "item-marker": "./app/components/item-marker.vue",
            "glossary-index": "./app/views/glossary-index.js",
            "glossary-details": "./app/views/glossary-details.js",
            "editor-plugin": "./app/components/editor-plugin.js",
            "editor-plugin-tinymce": "./app/components/tinymce-plugin.js"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        module: {
            loaders: [
                {test: /\.vue$/, loader: "vue"},
                {test: /\.js$/, exclude: /node_modules/, loader: "babel-loader"}
            ]
        }
    }

];