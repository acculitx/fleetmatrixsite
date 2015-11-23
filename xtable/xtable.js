var sources = ["trips", "vigilance", "total", "severe", "bellcurve", "tendency"];
var commonFields = ["Date", "id", "Driver", "Group", "Company"];


function onLoadXtable() {
  xtable = new Xtable;
  xtable.init();
}

var Xtable = function() {

  this.globalRowcount = 0;
  this.urlParams = new UrlParams();

  this.init = function(_p, _row_count) {

    var p = _p == null ?  {} : _p;
    this.setupDatePicker();
    this.setupSource();
    this.setupLinkToTrends();
    this.getData(p, "xtable_content", _row_count);
  };

  this.setupLinkToTrends = function () {
     var wheres = this.urlParams.get("where[]", []);
     for (var i=0; i<wheres.length; i++) {
       var where = unescape(wheres[i]);
       var re = /`id`="(.*)"/;
       var matches = re.exec(where);
       if (matches && matches.length) {
          var id = matches[1];
          $("#trends_link").attr("href", "../trends/index.html?d=" + id);
       } 
       
     }
  }

  this.setupSource = function() {
    this.source = this.urlParams.get("table", "trips");
    var s = "";
    for (var i = 0; i < sources.length; i++) {
      var thisSource = sources[i];
      if (i > 0) s += " | ";
      if (this.source == thisSource) {
        s += "<span class='selected_source_menu'>" + thisSource + "</span>";
      } else {
        // Need to remove the sorts and selects that are not applicable to the new source.
        var href = "index.html?table=" + thisSource + "&t0=" + this.urlParams.get("t0", "") + "&t1=" + this.urlParams.get("t1", "");
        var wheres = this.urlParams.get("where[]", []);
        for (var j = 0; j < wheres.length; j++) {
          var where = wheres[j];
          for (k = 0; k < commonFields.length; k++) {
            var field = commonFields[k];
            if (where.indexOf(field) != -1)
              href += "&where[]=" + encodeURIComponent(where);
          }
        }
        s += "<span class='source_menu'><a href='" + href + "'>" + thisSource + "</a></span>";
      }
    }
    $("#source_selector").html(s);

  }

  this.setupDatePicker = function() {
    var me = this;

    $("#date-range0").dateRangePicker({})
      .bind('datepicker-change', function(event, obj) {
        me.t0 = obj.date1;
        me.t1 = obj.date2;
      })
      .bind('datepicker-closed', function(event, obj) {
        console.log("date picker closed");
        console.log("t0=" + me.t0 + " t1=" + me.t1);
        me.urlParams.put("t0", me.t0.yyyymmdd());
        me.urlParams.put("t1", me.t1.yyyymmdd());

        // Remove any previous date selection in the where clause.
        var wheres = me.urlParams.get("where[]", []);
        for (var i=0; i<wheres.length; i++) {
           var where = wheres[i];
           // Should check for "Date=" not just date.
           if (where.indexOf("Date") != -1) {
             me.urlParams.removeElement("where[]", where);
           }
        }

        var href = "index.html?" + me.urlParams.generateString();
        navigateTo(href);
      });

    // Set start and end dates from url params.
    // If not defined in params, it's one week ago to now.
    var t0 = this.urlParams.get("t0", "");
    if (t0 == "") {
      this.t0 = new Date();
      this.t0.setDate(this.t0.getDate() - 180); // XXX was - 7
    } else
      this.t0 = new Date(t0);

    var t1 = this.urlParams.get("t1", "");
    if (t1 == "")
      this.t1 = new Date();
    else
      this.t1 = new Date(t1);

    $("#date-range0").val(this.t0.yyyymmdd() + " to " + this.t1.yyyymmdd());
  }

  this.getData = function(p, divName, _row_count) {
    var me = this;
    p.table = this.source;
    p.start_row = parseInt(this.urlParams.get("start_row", 0));
    p.row_count = (_row_count == null)
      ? parseInt(this.urlParams.get("row_count", 20))
      : _row_count;
    p["where"] = this.urlParams.get("where[]", []);
    p["sort"] = this.urlParams.get("sort[]", []);
    p["t0"] = this.t0.yyyymmdd();
    p["t1"] = this.t1.yyyymmdd();

    $.ajax({
      url: '../xtable/xtable.php',
      data: p,
      success: function(data) {
        me.displayData(data, divName);
        me.pagingControls(p);
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
    var headers = [];
    var lines = data.split("\n");
    this.resultsSetRowcount = lines.length;
    for (var i = 0; i < lines.length; i++) {
      var line = lines[i];
      if (line == '') continue;
      // First line has headers.  Second line and beyond
      // has the actual rows of data.
      s += "<tr>";
      var cols = line.split("\t")
      for (var j = 0; j < cols.length; j++) {
        var cellval = cols[j];
        if (i == 0) {
          headers.push(cellval);
          s += this.printHeader(cellval);
        } else {
          s += this.printCell(cellval, headers[j]);
        }
      }
      s += "</tr>";
    }

    return s;
  }

  this.printHeader = function(cellval) {
    var thisSort = "`" + cellval + "`";
    var cellStyle = "normal_header";
    var urlParams = new UrlParams();
    var nextUrlParams = new UrlParams();
    var sort = urlParams.get("sort[]", []);
    var found = false;
    var arrow = "";

    for (var i = 0; i < sort.length; i++) {
      if (sort[i].indexOf(cellval) == 1) {
        found = true;
        if (sort[i].indexOf(" DESC") != -1) {
          thisSort += " ASC";
          nextUrlParams.put("sort[]", thisSort);
          arrow = "&#9661;";
        } else {
          arrow = "&#9651;";
        }

        nextUrlParams.removeElement("sort[]", sort[i]);
        cellStyle = "selected_header";
      }
    }

    if (!found) {
      thisSort += " DESC";
      nextUrlParams.put("sort[]", thisSort);
    }

    var href = "index.html?" + nextUrlParams.generateString()
    return "<td class='" + cellStyle + "' onclick='navigateTo(\"" + href + "\");'>" + cellval + "<br>" + arrow + "</td>";
  }

  this.printCell = function(cellval, header) {
    var thisWhere = encodeURIComponent("`" + header + "`=\"" + cellval + "\"");
    var cellStyle = "normal_cell";
    var urlParams = new UrlParams();
    var where = urlParams.get("where[]", []);
    if (where.indexOf(thisWhere) == -1)
      urlParams.put("where[]", thisWhere);
    else {
      urlParams.removeElement("where[]", thisWhere);
      cellStyle = "selected_cell";
    }
    urlParams.put("start_row", 0);
    var href = "index.html?" + urlParams.generateString();
    return "<td class='" + cellStyle + "'><a class='cell_link' href='" + href + "'>" + cellval + "</a></td>";
  }

  this.pagingControls = function(p) {
    var nextStart = p.start_row + p.row_count;
    var prevStart = p.start_row - p.row_count;
    var urlParams = new UrlParams();
    urlParams.put("start_row", prevStart);
    var s = "";
    if (prevStart >= 0) {
      s += "<a href='index.html?" + urlParams.generateString() + "'>previous</a>";
    } else {
      s += "previous";
    }
    s += " | ";

    if (this.resultsSetRowcount < p.row_count)
      s += "next";
    else {
      urlParams.put("start_row", nextStart);
      s += "<a href='index.html?" + urlParams.generateString() + "'>next</a>";
    }

    urlParams.put("start_row", p.start_row);
    urlParams.put("row_count", 20);
    var rowCount = p.row_count == 20 ? "20" : "<a href='index.html?" + urlParams.generateString() + "'>20</a>";

    urlParams.put("row_count", 100);
    rowCount += " | ";
    rowCount += p.row_count == 100 ? "100" : "<a href='index.html?" + urlParams.generateString() + "'>100</a>";

    urlParams.put("row_count", 1000);
    rowCount += " | ";
    rowCount += p.row_count == 1000 ? "1000" : "<a href='index.html?" + urlParams.generateString() + "'>1000</a>";

    s += "<div class='row_count'>" + rowCount + "</div>";;

    $(".paging_controls").html(s);
  }

}

var UrlParams = function() {

  this.init = function() {
    this.vars = [];
    var hashes = [];
    var question = window.location.href.indexOf('?');
    if (question == -1)
      return;
    var paramString = window.location.href.slice(question + 1);
    if (paramString == "")
      return;
    // Remove an anchor if it's there.
    var hash = paramString.indexOf("#");
    if (hash != -1)
      paramString = paramString.substring(0, hash);
    hashes = paramString.split('&');
    for (var i = 0; i < hashes.length; i++) {
      console.log("hashes=" + hashes[i]);
      var keyval = hashes[i].split('=');
      var key = keyval[0];
      var val = unescape(keyval[1]);
      if (key.indexOf("[]") != -1) {
        if (!this.vars[key]) {
          this.vars[key] = [];
        }
        this.vars[key].push(val);
      } else {
        this.vars[key] = val;
      }
    }
  }

  this.init();

  this.removeElement = function(key, val) {
    for (var i = 0; i < this.vars[key].length; i++)
      if (this.vars[key][i] == val) {
        this.vars[key].remove(i);
        break;
      }
  }

  this.remove = function(key) {
    // Works?
    delete this.vars[key];
  }

  this.put = function(key, newVal) {
    if (key.indexOf("[]") != -1) {
      if (!this.vars[key])
        this.vars[key] = [];
      this.vars[key].push(newVal);
    } else
      this.vars[key] = newVal;
  }

  this.get = function(key, defaultVal) {
    return this.vars[key] ? this.vars[key] : defaultVal;
  }

  this.generateString = function() {
    var s = "";
    for (var key in this.vars) {
      if (this.vars.hasOwnProperty(key)) {
        if (key.indexOf("[]") != -1) {
          for (var i = 0; i < this.vars[key].length; i++) {
            var val = this.vars[key][i];
            s += "&" + key + "=" + escape(val);
          }
        } else {
          s += "&" + key + "=" + escape(this.vars[key]);
        }
      }
    }
    // Remove initial "&".
    return s.substring(1);
  }

}

function navigateTo(href) {
  document.location.href = href;
}
// Array Remove - By John Resig (MIT Licensed)
Array.prototype.remove = function(from, to) {
  var rest = this.slice((to || from) + 1 || this.length);
  this.length = from < 0 ? this.length + from : from;
  return this.push.apply(this, rest);
};

Date.prototype.yyyymmdd = function() {
  var yyyy = this.getUTCFullYear().toString();
  var mm = (this.getUTCMonth() + 1).toString(); // getMonth() is zero-based
  var dd = this.getUTCDate().toString();
  return yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]); // padding
};
