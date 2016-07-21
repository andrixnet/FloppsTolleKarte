/*jslint
  regexp: true
  indent: 4
*/

/*global
  $,
  Cookies, Line, Markers
*/

var Lines = {};
Lines.m_map = null;
Lines.m_nextLineId = 0;
Lines.m_lines = [];


Lines.init = function (themap) {
    'use strict';

    Lines.m_map = themap;
    Lines.m_nextLineId = 0;
    Lines.m_lines = [];
};


Lines.newLine = function (source, target) {
    'use strict';

    var m1 = Markers.getById(source),
        m2 = Markers.getById(target);

    if (!m1 || m1.isFree()) {
        source = -1;
    }
    if (!m2 || m2.isFree()) {
        target = -1;
    }

    this.m_lines.push(new Line(Lines.m_map, this.m_nextLineId, source, target));
    this.m_nextLineId += 1;
    this.saveCookie();
};


Lines.getLineIndex = function (id) {
    'use strict';

    var index, line;

    for (index = 0; index < this.m_lines.length; index += 1) {
        line = this.m_lines[index];
        if (line && line.getId() === id) {
            return index;
        }
    }

    return -1;
};


Lines.getLineById = function (id) {
    'use strict';

    var index = this.getLineIndex(id);
    if (index < 0) {
        return null;
    }
    return this.m_lines[index];
};


Lines.getLinesText = function () {
    'use strict';

    var a = [];
    this.m_lines.map(function (line) {
        if (line) {
            a.push(line.getEndpointsString());
        }
    });
    return a.join("*");
};


Lines.saveCookie = function () {
    'use strict';

    Cookies.set("lines", this.getLinesText(), {expires: 30});
};


Lines.selectLineSourceById = function (id, markerId) {
    'use strict';

    this.getLineById(id).setSource(markerId);
    this.saveCookie();
};


Lines.selectLineSource = function (id) {
    'use strict';

    var markerId = -1,
        opt = $("#dynlinesource" + id + " option:selected");

    if (opt) {
        markerId = parseInt(opt.val(), 10);
    }

    this.selectLineSourceById(id, markerId);
};


Lines.selectLineTargetById = function (id, markerId) {
    'use strict';

    this.getLineById(id).setTarget(markerId);
    this.saveCookie();
};


Lines.selectLineTarget = function (id) {
    'use strict';

    var markerId = -1,
        opt = $("#dynlinetarget" + id + " option:selected");

    if (opt) {
        markerId = parseInt(opt.val(), 10);
    }

    this.selectLineTargetById(id, markerId);
};


Lines.updateLinesMarkerMoved = function (markerId) {
    'use strict';

    this.m_lines.map(function (line) {
        if (line) {
            line.updateMarkerMoved(markerId);
        }
    });
};


Lines.updateLinesMarkerAdded = function () {
    'use strict';

    this.m_lines.map(function (line) {
        if (line) {
            line.updateMarkerAdded();
        }
    });
};


Lines.updateLinesMarkerRemoved = function (markerId) {
    'use strict';

    this.m_lines.map(function (line) {
        if (line) {
            line.updateMarkerRemoved(markerId);
        }
    });
    this.saveCookie();
};


Lines.updateLine = function (id) {
    'use strict';

    var index = this.getLineIndex(id);
    if (index < 0) {
        return;
    }

    this.m_lines[index].update();
};


Lines.deleteLine = function (id) {
    'use strict';

    $('#dynLine' + id).remove();

    var index = this.getLineIndex(id);
    if (index < 0 || !this.m_lines[index]) {
        return;
    }

    this.m_lines[index].clearMapObject();
    this.m_lines[index] = null;

    this.saveCookie();
};


Lines.deleteAllLines = function () {
    'use strict';

    var self = this;
    this.m_lines.map(function (line) {
        if (line) {
            self.deleteLine(line.getId());
        }
    });
};
