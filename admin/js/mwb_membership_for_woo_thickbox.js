jQuery(document).ready(function($){
    //alert('hl');
    var BaseModalView = Backbone.View.extend({

        id: 'base-modal',
        className: 'modal fade hide',
        template: 'modals/BaseModal',
    
        events: {
          'hidden': 'teardown'
        },
    
        initialize: function() {
          _(this).bindAll();
          this.render();
        },
    
        show: function() {
          this.$el.modal('show');
        },
    
        teardown: function() {
          this.$el.data('modal', null);
          this.remove();
        },
    
        render: function() {
          this.getTemplate(this.template, this.renderView);
          return this;
        },
    
        renderView: function(template) {
          this.$el.html(template());
          this.$el.modal({show:false}); // dont show modal on instantiation
        }
     });
});