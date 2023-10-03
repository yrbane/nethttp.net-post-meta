(function($){
    $(document).ready(function(){
        if ($('#post-metas').length) {
            //var postMetas = postMetaData.postMetas;
            //var postMetasType = postMetaData.postMetaTypes;

            $('#add-post-meta').on('click', function(){
                addRow([]);
            });

            for(var i in postMetas){
                addRow(postMetas[i]);
            }

            function addRow(field) {
                var selectBoxOption = postMetasType.map(function(type) {
                    return '<option ' + (type === field['type'] ? 'selected' : '') + '>' + type + '</option>';
                }).join('');

                var contextBoxOption = (['normal', 'side', 'advanced']).map(function(context) {
                    return '<option ' + (context === field['context'] ? 'selected' : '') + '>' + context + '</option>';
                }).join('');

                var row = '<tr>\
                    <td><input type="text" name="name[]" placeholder="PostMeta name" value="' + (field['name'] || '') + '"/></td>\
                    <td><input type="text" name="description[]" placeholder="PostMeta description" value="' + (field['description'] || '') + '"/></td>\
                    <td><label>Type: <select name="type[]">' + selectBoxOption + '</select></label></td>\
                    <td><label>Context: <select name="context[]">' + contextBoxOption + '</select></label></td>\
                </tr>';

                $('#post-metas').append(row);
            }
        }
    });
})(jQuery);