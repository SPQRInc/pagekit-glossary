window.item = {

    el: '#item',

    data: function () {
        return {
            data: window.$data,
            item: window.$data.item,
            sections: []
        }
    },

    created: function () {

        var sections = [];

        _.forIn(this.$options.components, function (component, name) {

            var options = component.options || {};

            if (options.section) {
                sections.push(_.extend({name: name, priority: 0}, options.section));
            }

        });

        this.$set('sections', _.sortBy(sections, 'priority'));

        this.resource = this.$resource('api/glossary/item{/id}');
    },

    ready: function () {
        this.tab = UIkit.tab(this.$els.tab, {connect: this.$els.content});
    },

    methods: {

        save: function () {
            var data = {item: this.item, id: this.item.id};

            this.$broadcast('save', data);

            this.resource.save({id: this.item.id}, data).then(function (res) {

                var data = res.data;

                if (!this.item.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/glossary/item/edit', {id: data.item.id}))
                }

                this.$set('item', data.item);

                this.$notify('Item saved.');

            }, function (res) {
                this.$notify(res.data, 'danger');
            });
        }

    },

    components: {
        settings: require('../../components/item-edit.vue')
    }
};

Vue.ready(window.item);