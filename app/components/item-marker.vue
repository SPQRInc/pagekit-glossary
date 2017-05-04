<template>
    <form class="uk-form uk-form-stacked" v-validator="formMarker" @submit.prevent = "add | valid">
        <div class="uk-form-row">
        <div class="uk-grid" data-uk-margin>
            <div class="uk-width-large-1-2">
                <input class="uk-input-large"
                       type="text"
                       placeholder="{{ 'Marker' | trans }}"
                       name="marker"
                       v-model="newMarker"
                       v-validate:required>
                <p class="uk-form-help-block uk-text-danger" v-show="formMarker.marker.invalid">
                    {{ 'Invalid value.' | trans }}</p>
            </div>
            <div class="uk-width-large-1-2">
                <div class="uk-form-controls">
                    <span class="uk-align-right">
                        <button class="uk-button" @click.prevent = "add | valid">
                            {{ 'Add' | trans }}
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </form>
    <hr/>
    <div class="uk-alert"
         v-if="!item.marker.length">{{ 'You can add your first marker using the input field above. Go ahead!' | trans }}
    </div>
    <ul class="uk-list uk-list-line" v-if="item.marker.length">
        <li v-for="mark in item.marker">
            <input class="uk-input-large"
                   type="text"
                   placeholder="{{ 'Marker' | trans }}"
                   v-model="mark">
            <span class="uk-align-right">
                <button class="uk-button uk-button-danger" @click.prevent = "remove(mark)">
                    <i class="uk-icon-remove"></i>
                </button>
            </span>
        </li>
    </ul>
</template>

<script>

module.exports = {

    section: {
        label: 'Marker',
        priority: 100
    },

    props: ['item', 'data'],

    data: function () {
        return {
            marker: this.item.marker,
            newMarker: ''
        }
    },


    methods: {
        add: function add(e) {

            e.preventDefault();
            if (!this.newMarker) return;
            this.item.marker.push(this.newMarker);
            this.newMarker = null;
        },
        remove: function (mark) {
            this.item.marker.$remove(mark);
        }
    }
};

window.item.components['item-marker'] = module.exports;

</script>