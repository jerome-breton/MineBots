// Objet appli angular
var robotApp = angular.module('robotApp', []);

robotApp.controller('RobotListCtrl', function($scope) {
	console.log('coucou');
	$scope.robots = [ {
		'id' : 'r2d2',
		'name' : 'I robot',
		'score' : 3,
		'health' : 100,
		'minerals' : 0,
		'message' : "Hello world"
	},
	{
		'id' : 'r2d32',
		'name' : 'I rosdsdbot',
		'score' : 2,
		'health' : 100,
		'minerals' : 0,
		'message' : "Hello world"
	}];
	
	$scope.scoreProp = 'score';
	$scope.addRobot = function(hash, data) {
		var found = false;
		 angular.forEach($scope.robots, function(robot) {
			 if (robot.id == hash) {
				 robot.score = data.score;
				 robot.health = data.life * 100;
				 robot.minerals = data.minerals;
				 robot.message = data.message;
				 found = true;
			 }
		});
		 if(! found)                {
			
			 $scope.robots.push({
					'id': data.id,
					'name' : data.name,
					'score' : data.score,
					'health' :data.health,
					'minerals' :data.minerals,
					'message' : data.message,
						});
			
		 }
		 
		
	};
});

