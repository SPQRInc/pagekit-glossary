<template>
    <div class="uk-grid pk-grid-large pk-width-sidebar-large uk-form-stacked" data-uk-grid-margin>
        <div class="pk-width-content">

            <div class="uk-form-row">
                <input class="uk-width-1-1 uk-form-large" type="text" name="title" :placeholder="'Enter Title' | trans"
                       v-model="item.title" v-validate:required>
                <p class="uk-form-help-block uk-text-danger"
                   v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>
            </div>
            <div class="uk-form-row">
                <v-editor id="item-content" :value.sync="item.content"
                          :options="{markdown : item.data.markdown}"></v-editor>
            </div>
            <div class="uk-form-row">
                <label for="form-item-excerpt" class="uk-form-label">{{ 'Excerpt' | trans }}</label>
                <div class="uk-form-controls">
                    <v-editor id="item-excerpt" :value.sync="item.excerpt"
                              :options="{markdown : item.data.markdown, height: 250}"></v-editor>
                </div>
            </div>

        </div>
        <div class="pk-width-sidebar">
            <div class="uk-panel">
                <div class="uk-form-row">
                    <label for="form-slug" class="uk-form-label">{{ 'Slug' | trans }}</label>
                    <div class="uk-form-controls">
                        <input id="form-slug" class="uk-width-1-1" type="text" v-model="item.slug">
                    </div>
                </div>
                <div class="uk-form-row">
                    <label for="form-status" class="uk-form-label">{{ 'Status' | trans }}</label>
                    <div class="uk-form-controls">
                        <select id="form-status" class="uk-width-1-1" v-model="item.status">
                            <option v-for="(id, status) in data.statuses" :value="id">{{status}}</option>
                        </select>
                    </div>
                </div>
                <div class="uk-form-row">
                    <span class="uk-form-label">{{ 'Options' | trans }}</span>
                    <div class="uk-form-controls">
                        <label><input type="checkbox" v-model="item.data.markdown"
                                      value="1"> {{ 'Enable Markdown' | trans }}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

module.exports = {

    props: ['item', 'data', 'form'],

    section: {
        label: 'Item'
    }

};

</script>