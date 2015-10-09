$(document).ready(function() {
  refreshPage();
});

function refreshPage() {
  xtable = new Xtable;
  xtable.init();
}

var Xtable  = function() {

  this.init = function() {
    var p = {};
    this.getData(p, "content");
  };

  this.getData = function(p, divName) {
    var me = this;
    p.table = "fleet_trip";
    p.start = 0;
    p.end = 10;

    $.ajax({
      url: 'xtable.php',
      data: p,
      success: function(data) {
        me.displayData(data, divName);
      }
    });
  };

  this.displayData = function(data, divName) {
    var report = this.parseData(data, divName);

    var s = "<table class='time_table'>" + report + "</table>";
    $("#" + divName).html(s + "<p/>");
  }

  this.parseData = function (data, divName) {
     var s= "";
     var lines = data.split("\n");
     for (var i=0; i<lines.length; i++) {
       var line = lines[i];
       var tr = (i == 0) ? "<tr class='heading'>" : "<tr>";
       s += tr;
       var cols = line.split("\t")
       for (var j=0; j<cols.length; j++)  {
            s += "<td>" + cols[j] + "</td>";
       }
       s += "</tr>";
     }

     return s;
  }
 }

$.urlParam = function(name) {
  var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
  if (results == null) {
    return null;
  } else {
    return results[1] || 0;
  }
}

