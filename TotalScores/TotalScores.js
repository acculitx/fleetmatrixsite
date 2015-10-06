$(document).ready(function() {
  refreshPage();
});

function refreshPage() {
  totalScores = new TotalScores();
  totalScores.init();
}

var TotalScores = function() {
  this.level = "c";
  this.nextLevel = "g";
  this.fixedParams = "";

  this.init = function() {
    var p = {};
    var c = $.urlParam('c');
    var g = $.urlParam('g');
    var d = $.urlParam('d');
    var linkBack = "";
    if (c) {
      if (!g) totalScores.getData(p, "top_content");
      p.c = c;
      if (c != "*") this.fixedParams += "&c=" + c;
    }
    if (g) {
      if (!d) totalScores.getData(p, "top_content");
      this.level = "g";
      this.nextLevel = "d";
      p.g = g;
      if (g == "*") linkBack = "c=*";
      if (g != "*") this.fixedParams += "&g=" + g;
    }
    if (d) {
      if (d == "*") totalScores.getData(p, "top_content");
      this.level = "d";
      this.nextLevel = "";
      p.d = d;
      if (d != "*") totalScores.getData(p, "top_content");
      if (d == "*") linkBack = "c=" + c + "&g=*";
      if (d != "*") {
        this.fixedParams += "&d=" + d;
        linkBack = "c=" + c + "&g=" + g + "&d=*";
      }
    }

    if (linkBack != "") {
      var link = "<a href=\"index.html?" + linkBack + "\">Back</a>";
      $("#linkback").html(link);
    }

    if (!d || d == "*")
      totalScores.getData(p, "content");
  };

  this.getData = function(p, divName) {
    var me = this;
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    if (start_date) p.t0 = start_date;
    if (end_date) p.t1 = end_date;
    $.ajax({
      url: 'TotalScores.php',
      // url: 'DailyMoving.php',
      // url: 'Vigilance.php',
      data: p,
      success: function(data) {
        me.displayData(data, divName);
      }
    });
  };

  this.displayData = function(data, divName) {
    var report = this.parseData(data);

    var s = "<table>" + report + "</table>";
    $("#" + divName).html(s + "<p/>");
  }

  this.parseData = function (data) {
     var s= "";
     var lines = data.split("\n");
     for (var i=0; i<lines.length; i++) {
       var line = lines[i];
       s += "<tr>";
       var cols = line.split("\t");
       for (var j=0; j<cols.length; j++) {
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

function displayChart(name, total, aggressive, vigilance) {
var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'graph_container',
        backgroundColor: null
    },

    title: {
        text: name == "" ? "All Subscribers" : name,
        style: {
            color: '#CCC'
        }
    },
    xAxis: {
//        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
      tickInterval: 0
    },
    
    yAxis: {
        labels: {
            style: {
                color: '#CCC'
            }
        },
        gridLineColor: '#333',
        max: 10,
        min: 0,
        tickInterval: 1
        
    },

    series: [{
        data: total, // [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
        color: 'cyan',
        name: "total",
        shadow: {
            color: 'cyan',
            width: 10,
            offsetX: 0,
            offsetY: 0
        }
        
    }, {
        data: aggressive, // [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4].reverse(),
        color: 'red',
        name: 'aggressivity',
        shadow: {
            color: 'red',
            width: 10,
            offsetX: 0,
            offsetY: 0
        }
        
    }, {
        data: vigilance, // [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4].reverse(),
        color: 'green',
        name: "vigilance",
        shadow: {
            color: 'green',
            width: 10,
            offsetX: 0,
            offsetY: 0
        }
  }
]

});
}
