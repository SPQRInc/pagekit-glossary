module.exports = {

    plugin: true,

    data: function () {
        return {
            helper: this.$parent.$options.utils['item-helper'].methods
        };
    },

    created: function () {
        var vm = this, editor = this.$parent.editor;

        if (!editor || !editor.htmleditor) {
            return;
        }

        this.items = [];

        editor.addButton('glossary', {
            title: 'Glossary',
            label: '<i class="uk-icon-book"></i>'
        });

        editor.options.toolbar.push('glossary');

        editor
            .on('action.glossary', function (e, editor) {
                vm.openModal(_.find(vm.items, function (item) {
                    return item.inRange(editor.getCursor());
                }));
            })
            .on('render', function () {
                vm.items = editor.replaceInPreview(/\(glossary\)(\{.+?\})/gi, vm.replaceInPreview);
            })
            .on('renderLate', function () {
                while (vm.$children.length) {
                    vm.$children[0].$destroy();
                }

                Vue.nextTick(function () {
                    editor.preview.find('glossary-preview').each(function () {
                        vm.$compile(this);
                    });
                });
            });

        editor.debouncedRedraw();
    },

    methods: {

        openModal: function (item) {
            var vm = this, editor = this.$parent.editor, cursor = editor.editor.getCursor();

            if (!item) {
                item = {
                    replace: function (value) {
                        editor.editor.replaceRange(value, cursor);
                    }
                };
            }

            new this.$parent.$options.utils['item-picker']({
                parent: this,
                data: {
                    item: item
                }
            })
                .$mount()
                .$appendTo('body')
                .$on('select', function (item) {
                    var content, itemInfo;
                    itemInfo = vm.helper.itemInfoFromPickerSelection(item)
                    content = '(glossary)' + JSON.stringify(itemInfo);
                    item.replace(content);
                });
        },
        replaceInPreview: function (data, index) {
            var item, parsed = {};

            try {
                parsed = JSON.parse(data.matches[1]);
            } catch (e) {
            }

            item = this.helper.flatToNestedItemInfo(parsed);

            if(item.id) {
                data.id = item.id;
            }

            if(item.data) {
                data.data = item.data
            }

            return '<glossary-preview index="' + index + '"></glossary-preview>';
        }

    },

    components: {
        'glossary-preview': require('./glossary-preview.vue')
    }

};

window.Editor.components['editor-plugin'] = module.exports;
window.Editor.utils['item-picker'] = Vue.extend(require('./item-picker.vue'));
window.Editor.utils['item-helper'] = require('./item-helper.js');