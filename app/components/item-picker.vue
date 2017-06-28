<template>
    <div>
        <v-modal v-ref:modal :closed="close">
            <form class="uk-form uk-form stacked"  @submit.prevent="update">
                <div class="uk-modal-header">
                    <h2>{{ 'Insert Glossary Item' | trans }}</h2>
                </div>
                <div>
                    <div class="uk-form-row uk-grid uk-form-stacked">
                        <div class="uk-width-large-1-1">
                            <div class="uk-form-row">
                                <label for="form-item" class="uk-form-label">{{ 'Item' | trans }}</label>
                                <div class="uk-form-controls">
                                    <select id="form-item" class="uk-form-width-large" v-model="item.id">
                                        <option v-for="item in items" :value="item.id">{{ item.title }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label for="form-text" class="uk-form-label">{{ 'Text' | trans }}</label>
                                <div class="uk-form-controls uk-form-controls-text">
                                    <p class="uk-form-controls-condensed">
                                        <input id="form-text" type="text" v-model="item.data.text">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-footer uk-text-right">
                    <button class="uk-button uk-button-link uk-modal-close" type="button">{{ 'Cancel' | trans }}
                    </button>
                    <button class="uk-button uk-button-link"
                            type="submit">{{ 'Update' | trans }}
                    </button>
                </div>
            </form>
        </v-modal>
    </div>
</template>
<script>
module.exports = {

    data: function () {
        return {
            item: {
                id: -1,
                data: { text: '' }
            },
            items: []
        }
    },

    created: function () {
        this.$resource('api/glossary/item').get().then(function (result) {
            this.items = result.data.items;

            if (result.data.items.length && this.item.id < 0) {
                this.item.id = result.data.items[0].id;
            }
        });
    },

    ready: function () {
        this.$refs.modal.open();
    },

    methods: {
        close: function () {
            this.$destroy(true);
        },
        update: function () {
            this.$refs.modal.close();
            this.$emit('select', this.item);
        }
    }
};
</script>