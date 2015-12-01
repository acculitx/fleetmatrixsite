angular.module("app", [])

.directive('tree', function() {
  return {
    restrict: 'E',
    scope: false,
    templateUrl: 'tree.html'
  }
})

.run(['$anchorScroll', function($anchorScroll) {
  $anchorScroll.yOffset = 80;   // always scroll by 50 extra pixels
}])

.controller("TreeController", ['$scope', '$http', '$location', '$anchorScroll', '$window',
  function($scope, $http, $location, $anchorScroll, $window) {
  $scope.tree = [];
  $scope.items = [];
  $scope.window = $window;

  $scope.totalScores = new TotalScores();
  $scope.xtable = new Xtable;
    
  $http.get('report_pdo.php?proc=get_containers').success(function(data) {
      
    var hChildren = {};
    var hNodes = {};
    var theDriverId = -1;
      
    // Create hash of children and empty nodes.  
    data.forEach(function(currentValue, index, array) {
      var id = currentValue.id;
      $scope.nextId = id + 1;
      var id_parent = currentValue.id_parent;
      hNodes[currentValue.id] = { 
          name: currentValue.name, 
          nodes: [], 
          id: id,
          id_parent: id_parent,
          isOpen : true};
      if (id_parent != null) {
          if (hChildren[id_parent] == null) hChildren[id_parent] = [];
          hChildren[id_parent].push(currentValue);
      }
    });
      
    // Depth-first recursion through child nodes.  
    function recurseChildren (parent) {
        // No children?
        if (hChildren[parent.id] == null) return;
        
        // Already processed?
        if (hNodes[parent.id].nodes.length != 0) return;
        
        // Add each child, after adding all of its children to itself.
        hChildren[parent.id].forEach(function(currentValue) {
              recurseChildren(currentValue);
              hNodes[parent.id].nodes.push(hNodes[currentValue.id]);
        });        
    }
    
    // Start recursion.
    data.forEach(function(currentValue) {
        recurseChildren(currentValue);
    });
      
    // Publish top-level nodes.
    for (var key in hNodes) {
      if (hNodes[key].id_parent == null)
        $scope.tree.push(hNodes[key]);
    }    
  });
    
  $scope.toggle = function(data) {
      // Show or hide all children.
      if (data.nodes.length == 0)
          return;
      
      data.isOpen = !data.isOpen;
  //    var display = data.isOpen ? "block" : "none";

  };

  $scope.getDescendants = function(data) {
      // Recursively gather all descendants.
    var nodeIds = [data.id];
    function gatherChildren(data) {
      data.nodes.forEach(function(currentValue) {
      nodeIds.push(currentValue.id);
      gatherChildren(currentValue);
     });
    }
    gatherChildren (data);

    // Create a string with all the containers.
    var first = true;
    var container_ids = "'";
    nodeIds.forEach(function(currentValue) {
        if (!first) container_ids += ",";
        first = false;
        container_ids += currentValue;
    });
    container_ids += "'";

    return container_ids;

  }
    
  $scope.getItemsAndDrivers = function(data) {
    $scope.items = [];
    $scope.selectedNode = data.id;
    $scope.selectedData = data;
    $scope.selectedVehicleId = -1;
    $scope.selectedDriverId = -1;
      
    container_ids = $scope.getDescendants(data);
      
    // Request items with those containers.
    var url = 'report_pdo.php?proc=get_items&params=' + container_ids;
    $http.get(url).success(function(data) {
        $scope.items = data;
        if ($scope.items.length > 0)
          $scope.setDriverId($scope.items[0].driver_id, $scope.items[0].id);
    });

    $scope.drivers = [];

    // Request items with those containers.
    var url = 'report_pdo.php?proc=get_drivers&params=' + container_ids;
    $http.get(url).success(function(data) {
        $scope.drivers = data;
    });
  };

  $scope.setDriverId = function (driver_id, vehicle_id) {
    $scope.selectedDriverId = driver_id;
    $scope.selectedVehicleId = vehicle_id;

    // Scroll the corresponding driver to view.
    $location.hash("driver_div_" + driver_id);
//    alert(angular.element('#driver_list')[0].scrollTop)
//    $anchorScroll.yOffset = 200;
//    $anchorScroll();
//    return;
    // Show total score trend graph.
    $scope.totalScores.init(driver_id);

    // Show xtable trip list.
    var thisWhere = encodeURIComponent("`id`=\"" + driver_id + "\"");
    var p2 = {};
    p2["where[]"] = thisWhere;
    $scope.xtable.init(p2, 5);
  };

  $scope.newNode = function (data) {
    var newId = $scope.nextId++;
    var newNode = {
      id: newId,
      id_parent: data.id,
      name:"new node",
      nodes:[]
      };
    data.nodes.push(newNode);
  };

  $scope.deleteNode = function(data) {

  }
     
}]);



