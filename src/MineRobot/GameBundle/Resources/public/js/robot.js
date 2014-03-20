

$(function() {
	var Robot = Backbone.Model.extend({
		defaults : function() {
			return {
				title : "empty robot...",
				score : 0
			};
		}
	});

	var RobotList = Backbone.Collection.extend({
		model : Robot,
		comparator : 'score'
	});

	var Robots = new RobotList;

	var RobotView = Backbone.View.extend({
		template : _.template($('#item-template').html()),

		
		render : function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
		
	});

	var AppView = Backbone.View.extend({

		el : $("#robots"),

		initialize : function() {

			Robots.fetch();
		},
		
		addOne : function(robot) {
			var view = new RobotView({
				model : robot
			});
			this.$("#robot-list").append(view.render().el);
		},
		addAll : function() {
			Robots.each(this.addOne, this);
		}

	});

	var App = new AppView;
	
	
	Robots.create({
        title : 'rrrr',
        score: 10
      });
});


