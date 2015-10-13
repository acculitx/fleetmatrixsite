$(document).ready(function() {
  radioSubmitsForm("ds");
  radioSubmitsForm("dr");
  radioSubmitsForm("ts");

  refreshPage();

  onresize();
});

function radioSubmitsForm(rbName) {
  $('input[name=' + rbName + ']').change(function() {
    $('form').submit();
  });
}

function onresize() {
  $("#graph_container").css("width", $(".time_table").css("width"));
}

$(window).resize(function() {
  onresize();
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

    if (d) {
      // Looks like we are double-escaping these params.  To fix! xxx
      var param = encodeURIComponent("`id`=\"" + d + "\"");
      param = escape(param);
      $("#drivers_link").attr("href", "xtable/index.html?where[]=" + param);
    }

    var ds = $.urlParam('ds');
    if (!ds) ds = "total";
    $("input[name=ds][value=" + ds + "]").attr('checked', 'checked');

    var dr = $.urlParam('dr');
    if (!dr) dr = "month";
    $("input[name=dr][value=" + dr + "]").attr('checked', 'checked');

    var ts = $.urlParam('ts');
    if (!ts) ts = "day";
    $("input[name=ts][value=" + ts + "]").attr('checked', 'checked');


    // Set hidden fields in set parameters form.
    if (c)
      $("#c").val(c);
    if (g)
      $("#g").val(g);
    if (d)
      $("#d").val(d);

    this.fixedParams = "ds=" + ds + "&dr=" + dr + "&ts=" + ts;

    this.ds = ds;
    p.ds = ds;

    if (ts == "month") {
      this.dateFormat = "%Y-%b";
    } else if (ts == "week") {
      this.dateFormat = "%Y-%u";
    }

    if (dr == "month") {
      this.start_date = " DATE_SUB(NOW(), INTERVAL 1 MONTH) ";
    } else if (dr == "sixmonths") {
      this.start_date = " DATE_SUB(NOW(), INTERVAL 6 MONTH) ";
    } else {
      this.start_date = " 2000-01-01 ";
    }
    this.end_date = " NOW() ";

    if (!c && !g && !d) c = "*";
    var linkBack = "";
    if (c) {
      if (!g) this.getData(p, "top_content");
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
        if (c && g) 
          linkBack = "c=" + c + "&g=" + g + "&d=*";
        else
           linkBack = "c=*";
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
    p.df = this.dateFormat;

    p.t0 = this.start_date;
    p.t1 = this.end_date;
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
    var report = this.parseData(data, divName);

    var s = "<table class='time_table'>" + report + "</table>";
    $("#" + divName).html(s + "<p/>");
  }

  this.parseData = function(data, divName) {
    var s = "";
    var lines = data.split("\n");
    var lastName = "";
    var series = [];
    var total = [];
    var numAggCols = this.ds == "vigilance" ? 6 : 3;
    for (var i = 0; i < lines.length; i++) {
      var line = lines[i];
      var tr = (i == 0) ? "<tr class='dateline'>" : "<tr>";
      s += tr;
      var cols = line.split("\t")

      // Use the left column expanded to 3 rows for names, rather than repeating them.
      var name = cols[1];
      if (this.level == 'c' && divName == "top_content")
        name = "All Companies";
      if (name && name != lastName) {
        var rowspan = (i == 0) ? 1 : numAggCols;

        var skipIt = this.level == "c" && divName == "top_content";
        if (!skipIt) {
          var link = "<a href='index.html?" + this.fixedParams + "&" + this.level + "=" + cols[0] + "&" + this.nextLevel + "=*'>" + name + "</a>";
          s += "<td rowspan='" + rowspan + "'>" + link + "</td>";
        }

        lastName = name;
      };;

      // Print the data grid.
      var end = (i == 0) ? cols.length - 1 : cols.length;
      var start = (divName == "top_content" && this.level == "c") ? 0 : 2;
      for (var j = start; j < end; j++) {
        s += "<td>" + cols[j] + "</td>";
        if (i >= 1 && i <= numAggCols) {
          var aggcol = cols[end - 1];
          if (j < end - 1) {
            if (!series[aggcol])
              series[aggcol] = [];
            if (cols[j])
              series[aggcol].push(parseFloat(cols[j]));
          }
        }
      }
      s += "</tr>";
    }

    if (divName == "top_content")
      displayChart(lastName, series);

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

function displayChart(name, input_series) {

  var colors = ["red", "yellow", "green", "blue", "orange", "purple"];
  var colorIdx = 0;
  var output_series = [];
  for (var aggcol in input_series) {
    var info = {
      data: input_series[aggcol],
      name: aggcol,
      color: colors[colorIdx++],
      shadow: {
        width: 5
      }
    };
    output_series.push(info);
  }

  var chart = new Highcharts.Chart({

    chart: {
      renderTo: 'graph_container',
      backgroundColor: null
    },

    title: {
      text: name == "" ? "All Subscribers" : name,
      style: {
        color: '#333'
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
      //        max: 10,
      //        min: 0,
      //        tickInterval: 1

    },

    series: output_series
  });
}
