function form(pCustomerId) {
    function optionTemplate(item) {
        return '<option value="' + item.id + '">' + item.description + '</option>'
    }
    jQuery(document).ready(function() {
        jQuery("#cust\\:cityId").cascade("#cust\\:departmentId",{
            ajax: {
                url: 'index.php',
                data: { q: '/location/cities' }
            },
            template: optionTemplate,
            match: function(val) {return val == this.departmentId;}
        });
        jQuery("#cust\\:locationId").cascade("#cust\\:cityId",{
            ajax: {
                url: 'index.php',
                data: { q: '/location/neighbourhoods' }
            },
            template: optionTemplate,
            match: function(val) {return val == this.cityId;}
        });
        $('#form').submit(function() {
            return false;
        });
        $('#postForm').click(function(e) {
            $.post('?q=/customer/save', $('#form').serializeArray(), function(r) {
                if (r.success) {
                    window.jowner.onClose(pCustomerId);
                }
                else if (r.messages) {
                    $.each(r.messages, function(field, msg) {
                        $('#cust\\:' + field)
                            .css({'background' : '#ff0000', 'color' : '#ffffff'})
                            .attr("title", msg);
                    });
                    // TODO: Message and i18n
                    alert('Algunos campos deben ser revisados');
                }
            }, 'json');
            e.preventDefault();
        });
        $('#closeWindow').click(function(e) {
            window.jowner.onClose(false);
            e.preventDefault();
        });
    });
}