var CubicSpline;
CubicSpline = function () {
    function p(f, d, e, k) {
        var h, j, b, l, i, a, g, c, m, o, n; if (f != null && d != null) { b = e != null && k != null; c = f.length - 1; i = []; o = []; g = []; m = []; n = []; j = []; h = []; l = []; for (a = 0; 0 <= c ? a < c : a > c; 0 <= c ? a += 1 : a -= 1) i[a] = f[a + 1] - f[a]; if (b) { o[0] = 3 * (d[1] - d[0]) / i[0] - 3 * e; o[c] = 3 * k - 3 * (d[c] - d[c - 1]) / i[c - 1] } for (a = 1; 1 <= c ? a < c : a > c; 1 <= c ? a += 1 : a -= 1) o[a] = 3 / i[a] * (d[a + 1] - d[a]) - 3 / i[a - 1] * (d[a] - d[a - 1]); if (b) { g[0] = 2 * i[0]; m[0] = 0.5; n[0] = o[0] / g[0] } else { g[0] = 1; m[0] = 0; n[0] = 0 } for (a = 1; 1 <= c ? a < c : a > c; 1 <= c ? a += 1 : a -= 1) { g[a] = 2 * (f[a + 1] - f[a - 1]) - i[a - 1] * m[a - 1]; m[a] = i[a] / g[a]; n[a] = (o[a] - i[a - 1] * n[a - 1]) / g[a] } if (b) { g[c] = i[c - 1] * (2 - m[c - 1]); n[c] = (o[c] - i[c - 1] * n[c - 1]) / g[c]; j[c] = n[c] } else { g[c] = 1; n[c] = 0; j[c] = 0 } for (a = e = c - 1; e <= 0 ? a <= 0 : a >= 0; e <= 0 ? a += 1 : a -= 1) { j[a] = n[a] - m[a] * j[a + 1]; h[a] = (d[a + 1] - d[a]) / i[a] - i[a] * (j[a + 1] + 2 * j[a]) / 3; l[a] = (j[a + 1] - j[a]) / (3 * i[a]) } this.x = f.slice(0, c + 1); this.a = d.slice(0, c); this.b = h; this.c = j.slice(0, c); this.d = l }
    }
    p.prototype.derivative = function () {
        var f, d, e, k, h; d = new this.constructor; d.x = this.x.slice(0, this.x.length); d.a = this.b.slice(0, this.b.length); h = this.c; e = 0; for (k = h.length; e < k; e++) { f = h[e]; d.b = 2 * f } h = this.d; e = 0; for (k = h.length; e < k; e++) { f = h[e]; d.c = 3 * f } f = 0; for (e = this.d.length; 0 <= e ? f < e : f > e; 0 <= e ? f += 1 : f -= 1) d.d = 0; return d
    };
    p.prototype.interpolate = function (f) {
        var d, e; for (d = e = this.x.length - 1; e <= 0 ? d <= 0 : d >= 0; e <= 0 ? d += 1 : d -= 1) if (this.x[d] <= f) break; f = f - this.x[d]; return this.a[d] + this.b[d] * f + this.c[d] * Math.pow(f, 2) + this.d[d] * Math.pow(f, 3)
    };
    return p
} ();