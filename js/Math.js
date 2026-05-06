MathJax.Hub.Config(
    {
        extensions: ["tex2jax.js"],
        jax: ["input/TeX", "output/HTML-CSS"],
        tex2jax:
        {
            inlineMath: [ ['$','$'], ["\\(","\\)"] ],
            displayMath: [ ['$$','$$'], ["\\[","\\]"] ],
            processEscapes: true
        },
                
        TeX:
        {
            extensions: ["AMSmath.js","AMSsymbols.js","noErrors.js","noUndefined.js"],
            Macros:
            {
                dif: '\\mathrm{d}',
                LiRE: '\\sf L\\kern-.2em\\lower-.2ex\\hbox{i}\\kern-.06emR\\kern-.04emE'
            }
        },
                
        "HTML-CSS": {
            //availableFonts: ["TeX"]
			availableFonts: [],
			preferredFont: null,
			webFont: "Neo-Euler"
        }
    });