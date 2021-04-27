!function (e) {
    const n = e(window), i = e("body"), s = e("#menu"), o = e("#sidebar"), t = e("#main");
    breakpoints({
        xlarge: ["1281px", "1680px"],
        large: ["981px", "1280px"],
        medium: ["737px", "980px"],
        small: ["481px", "736px"],
        xsmall: [null, "480px"]
    }), n.on("load", function () {
        window.setTimeout(function () {
            i.removeClass("is-preload")
        }, 100)
    }), s.appendTo(i).panel({
        hideOnClick: !0,
        hideOnSwipe: !0,
        hideOnEscape: !0,
        resetScroll: !0,
        resetForms: !0,
        swipe: "right",
        target: i,
        visibleClass: "is-menu-visible"
    });
    const l = e("#search"), a = l.find("input");
    i.on("click", '[href="#search"]', function (e) {
        e.preventDefault(), l.hasClass("visible") || (l[0].reset(), l.addClass("visible"), a.focus())
    }), a.on("keydown", function (e) {
        27 === e.keyCode && a.blur()
    }).on("blur", function () {
        window.setTimeout(function () {
            l.removeClass("visible")
        }, 100)
    });
    const r = e("#intro"), p = e("#nav"), d = e("#navheader"), u = e("#navmenu");
    breakpoints.on("<xlarge", function () {
        r.prependTo(t)
    }), breakpoints.on(">=xlarge", function () {
        r.prependTo(o)
    }), breakpoints.on("<=medium", function () {
        p.prependTo(u), p.addClass("links"), u.removeClass("is-hide")
    }), breakpoints.on(">medium", function () {
        p.prependTo(d), p.removeClass("links"), u.addClass("is-hide")
    });
    let c = document.getElementsByClassName("closebtn");
    for (let e = 0; e < c.length; e++) c[e].onclick = function () {
        let e = this.parentElement;
        e.style.opacity = "0", setTimeout(function () {
            e.style.display = "none"
        }, 600)
    }
}(jQuery);