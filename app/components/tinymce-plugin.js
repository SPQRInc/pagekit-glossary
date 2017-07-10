module.exports = {

	plugin: true,

	data: function () {
		return {
			helper: this.$parent.$options.utils['item-helper'].methods
		};
	},

	created: function () {
		var vm = this;

		if (typeof tinyMCE === 'undefined') {
			return;
		}

		this.$parent.editor.plugins.push ('-glossary');
		tinyMCE.PluginManager.add ('glossary', function (editor) {

			var showDialog = function () {
				var element = editor.selection.getNode (), item = {}, parsed = {};

				if (element.nodeName === 'DIV' && !!element.attributes['data-glossary'].value) {
					editor.selection.select (element);

					try {
						parsed = JSON.parse (vm.helper.decodeHTML (element.attributes['data-glossary'].value));
					} catch (e) {
					}

					item = vm.helper.flatToNestedItemInfo (parsed);
				}

				new vm.$parent.$options.utils['item-picker'] ({
					parent: vm,
					data: {
						item: item
					}
				}).$mount ()
				.$appendTo ('body')
				.$on ('select', function (item) {
					var itemInfo = vm.helper.itemInfoFromPickerSelection (item);

					editor.selection.setContent (
						vm.getItemContent (itemInfo)
					);

					editor.fire ('change');
				});
			};

			editor.addButton ('itemPicker', {
				tooltip: vm.$trans ('Insert Glossary Item'),
				icon: 'none uk-icon-book" style="font-family: FontAwesome;',
				onclick: showDialog
			});

			editor.addContextToolbar (function (element) {
				return element && element.nodeName === 'DIV' && !!element.attributes['data-glossary'].value;
			}, 'itemPicker');

			editor.addMenuItem ('itemPicker', {
				text: vm.$trans ('Insert Glossary Item'),
				icon: 'none uk-icon-book" style="font-family: FontAwesome;',
				context: 'insert',
				onclick: showDialog
			});
		});
	},

	methods: {
		getItemContent: function (itemInfo) {
			return '(glossary)%dataString%'.replace ('%dataString%', JSON.stringify (itemInfo));
		}
	}

};

window.Editor.components['editor-glossary'] = module.exports;