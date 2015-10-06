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
    var dateRange = this.findDateRange(report);
    var s = this.formatData(report, dateRange, divName);
    var s = "<table>" + s + "</table>";
    $("#" + divName).html(s + "<p/>");
  }

  this.findDateRange = function(report) {
    var r = {};
    for (var i = 0; i < report.items.length; i++) {
      var stats = report.items[i].stats;
      var yearmonths = stats[0];
      for (var j = 0; j < yearmonths.length; j++) {
        r[yearmonths[j]] = 1;
      }
    }
    return r;
  }

  this.parseData = function(data) {
    var report = {};
    var lines = data.split('\n');
    var stats = [];
    report.header = [];
    report.items = [];
    var id = -1;
    var name = '';
    for (var i = 0; i < lines.length; i++) {
      if (lines[i] == "") {
        continue;
      }
      var cols = lines[i].split('\t');

      // The first line has the column headers.
      if (i == 0) {
        for (j = cols.length - 4; j < cols.length; j++) {
          report.header.push(cols[j]);
          stats = [];
        }
        continue;
      }

      // The global query has no id or name.
      var newId = -1;
      var newName = "";
      if (cols.length > 4) {
        newId = cols[0];
        newName = cols[1];
      }

      if (newId != id && name != "") {
        // New item -- store current accumulation buffer and start new one.
        report.items.push(this.parseItem(id, name, stats, report.header));
        stats = [];
      }

      id = newId;
      name = newName;

      // Get stats for this item.
      var stat = [];
      for (var j = cols.length - 4; j < cols.length; j++) {
        stat.push(cols[j]);
      }
      stats.push(stat);
    }

    // Last item.
    report.items.push(this.parseItem(id, name, stats, report.header));

    return report;
  };

  this.parseItem = function(id, name, stats, header) {
    if (name == "") name = "All Companies";
    stats.push(header);
    return {
      "id": id,
      "name": name,
      "stats": transpose(stats)
    };
  }

  this.formatData = function(rows, dateRange, divName) {
    var s = "";
    for (var i = 0; i < rows.items.length; i++) {
      var item = rows.items[i];
      s += this.formatItem(item.id, item.name, item.stats, dateRange, i == 0, divName);
    }
    return s;
  }

  this.formatItem = function(id, name, stats, dateRange, first, divName) {
    var s = "";

    var dataDates = [];
    for (var i = 0; i < stats[0].length; i++) {
      dataDates[stats[0][i]] = 1;
    }
    var total = [];
    var aggressive = [];
    var vigilance = []
    var scoreChart = "<table class='score_chart'>";
    var start = first ? 0 : 1;
    //     start = 0;
    for (var i = start; i < stats.length; i++) {
      var line = stats[i];
      var idx = 0;
      var tr = (i == 0) ? "<tr class=\"dateline\">" : "<tr>";
      scoreChart += tr;
      var dates = Object.keys(dateRange);
      //    for (var j=0; j<line.length; j++) {
      for (var j = 0; j < dates.length; j++) {
        if (idx >= line.length) break;
        var key = dates[j];
        if (dataDates[key] == 1) {
          var score = line[idx++];
          scoreChart += "<td>" + score + "</td>"
          if (j < line.length - 1) {
            score = parseFloat(score);
            if (i == 1) total.push(score);
            if (i == 2) aggressive.push(score);
            if (i == 3) vigilance.push(score);
          }
        } else {
          scoreChart += "<td></td>";
        }
      }
      scoreChart += "</tr>";
    }
    scoreChart += "</table>";

    if (divName == 'top_content')
      displayChart(name, total, aggressive, vigilance);

    var href = "index.html?" + this.fixedParams + "&" + this.level + "=" + id;
    if (this.nextLevel != "") href += "&" + this.nextLevel + "=*";

    var nameHref = "<a href='" + href + "'>" + name + "</a>";

    var s = "<tr><td style='padding:20px;font-size:14pt'>" + nameHref + "</td><td>" + scoreChart + "</td></tr>";

    return s;
  }
}

function transpose(array) {
  if (array.length <= 0) return array;

  var newArray = array[0].map(function(col, i) {
    return array.map(function(row) {
      return row[i]
    })
  });
  return newArray;
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
