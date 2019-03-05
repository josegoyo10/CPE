function view(pCustomerId) {
    jQuery(document).ready(function() {
        $('#editCustomerButton').click(function(e) {
            var w = window.open('?q=/customer/edit&id='+pCustomerId, 'customerFormWindow', 'width=740,height=500');
            w.jowner = {
                onClose: function() {
                    w.close();
                    document.location.replace(document.location);
                }
            };
            w.focus();
            e.preventDefault();
        });
    });
}