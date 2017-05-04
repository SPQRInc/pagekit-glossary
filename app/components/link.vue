<template>

    <div class="uk-form-row">
        <label for="form-link-glossary" class="uk-form-label">{{ 'View' | trans }}</label>
        <div class="uk-form-controls">
            <select id="form-link-glossary" class="uk-width-1-1" v-model="link">
                <option value="@glossary">{{ 'Overview' | trans }}</option>
            </select>
        </div>
    </div>

</template>

<script>

module.exports = {

    link: {
        label: 'Glossary'
    },

    props: ['link'],

    ready: function () {
        this.$set('link', '@glossary');
    }

};

window.Links.components['glossary'] = module.exports;

</script>