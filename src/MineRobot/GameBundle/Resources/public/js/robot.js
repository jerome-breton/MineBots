// Objet appli angular
var robotApp = angular.module('robotApp', []);

robotApp.controller('RobotListCtrl', function($scope) {
	$scope.robots = [ {
		'id' : 'r2d2',
		'name' : 'I robot',
		'score' : 3,
		'health' : 100,
		'minerals' : 0,
		'message' : "Hello world"
	}, {
		'id' : 'r2d32',
		'name' : 'I rosdsdbot',
		'score' : 2,
		'health' : 100,
		'minerals' : 0,
		'message' : "Hello world"
	} ];

	// pour le tri
	$scope.scoreProp = 'score';
	
	// gestion ajout ou mise a jour des status de robots
	$scope.addRobot = function(hash, data) {
		var found = false;
		angular.forEach($scope.robots, function(robot) {
			if (robot.id == hash) {
				robot.score = data.score;
				robot.health = data.life * 100;
				robot.minerals = data.minerals;
				robot.message = data.message;
				robot.picture = data.picture;
				found = true;
			}
		});
		// si non trouvé on l'ajoute
		if (!found) {
			$scope.robots.push({
				'id' : data.id,
				'name' : data.name,
				'score' : data.score,
				'health' : data.health,
				'minerals' : data.minerals,
				'message' : data.message,
				'picture' : data.picture
			});
		}
	};
});
