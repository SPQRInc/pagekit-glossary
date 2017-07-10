<template>
    <a :href="glossary.url" @click.prevent="$parent.openModal(item)"><span v-html="item.data.title" v-if="item.data.title"></span><span v-html="glossary.title" v-else></span><span class="pk-icon-link pk-icon-hover"></span></a>
</template>

<script>
module.exports = {
    props: ['index'],

    data: function() {
        return {
            glossary: ''
        }
    },
    computed: {
        item: function() {
            return this.$parent.items[this.index] || {};
        },
    },
    ready: function () {
        this.$resource('api/glossary/item{/id}', {id: this.item.id}).get()
            .then(function(res){
                this.$set('glossary', res.data)
            });
    }
};

</script>