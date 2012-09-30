var Campaign = Campaign || {};

Campaign.Messages = new function() {
    var self = this;
    var add = function(strong, message, additionClass) {
        var node = $('<div class="alert fade in"><button type="button" class="close" data-dismiss="alert">×</button><strong></strong> <span><span></div>');
        node.addClass(additionClass);
        node.find('strong').text(strong);
        node.find('span').text(message);

        $('#alerts').prepend(node);
        setTimeout(function() { node.alert('close'); }, 3000);
    };


    this.addSuccess = function(strong, message) {
        add(strong, message, 'alert-success');
        return self;
    };

    this.addError = function(strong, message) {
        add(strong, message, 'alert-error');
        return self;
    };

    this.addInfo = function(strong, message) {
        add(strong, message, 'alert-info');
        return self;
    };
};