(function(){if(typeof(stlib.pixels)=="undefined"){stlib.pixels={stid:"__stid",firePixel:false,getCurrentURL:function(){return window.location.href},trimURL:function(a){return a.split(/\?|\&|\#/)[0]},getReferrerDomain:function(){var a=document.createElement("a");a.href=document.referrer;return a.hostname},getPxcelParams:function(d,c){var b=stlib.pixels.getCurrentURL();var a=stlib.pixels.trimURL(b);return" var pxcelData = { v0: '"+encodeURIComponent(d)+"', v1: '"+encodeURIComponent(stlib.pixels.getReferrerDomain())+"', v2: '"+encodeURIComponent("http://seg.sharethis.com/getSegment.php?purl="+encodeURIComponent(a)+"&rnd="+c)+"', v3: '"+encodeURIComponent(a)+"', v4: '"+encodeURIComponent(b)+"' };"},getRnd:function(){return(new Date()).getTime()},getPxcelTag:function(c){var b=stlib.pixels.getRnd();var a=window.top.location===window.location?window.location.toString():document.referrer;var d=a.split("/")[2];return"var pxscrpt = document.createElement('script'); pxscrpt.id = 'pxscrpt'; pxscrpt.async = true; pxscrpt.defer = true; pxscrpt.src = '//t.sharethis.com/1/d/t.dhj?rnd="+b+"&cid=c010&dmn="+d+"';document.body.appendChild(pxscrpt);"},getCookie:function(d){var b="(?:(?:^|.*;)\\s*"+d+"\\s*\\=\\s*([^;]*).*$)|^.*$";var a=new RegExp(b,"g");return document.cookie.replace(a,"$1")},isCookieSet:function(a){return stlib.pixels.getCookie(a)!==""},hasStid:function(){return stlib.pixels.isCookieSet(stlib.pixels.stid)},getIframeContents:function(b){var a=stlib.pixels.getRnd();var c=stlib.pixels.getPxcelParams(b,a);header="<!DOCTYPE html><html><head><title>ShareThis Segmenter</title></head>";return header+'<body onload="'+stlib.pixels.getPxcelTag(b)+'"><script>'+c+"<\/script></body>"},createSegmentFrame:function(){if(stlib.pixels.segmentframe||document.getElementById("stSegmentFrame")){return}try{stlib.pixels.segmentframe=document.createElement('<iframe name="stframe" allowTransparency="true" style="body{background:transparent;}" ></iframe>')}catch(c){stlib.pixels.segmentframe=document.createElement("iframe")}stlib.pixels.segmentframe.id="stSegmentFrame";stlib.pixels.segmentframe.name="stSegmentFrame";var d=document.head;stlib.pixels.segmentframe.frameBorder="0";stlib.pixels.segmentframe.scrolling="no";stlib.pixels.segmentframe.width="0px";stlib.pixels.segmentframe.height="0px";stlib.pixels.segmentframe.sandbox="allow-scripts allow-same-origin";stlib.pixels.segmentframe.setAttribute("style","display:none;");var b=stlib.data.get("stid","pageInfo");if(b){var a=stlib.pixels.getIframeContents(b);d.appendChild(stlib.pixels.segmentframe);stlib.pixels.segmentframe.contentWindow.document.open().write(a);stlib.pixels.segmentframe.contentWindow.document.close()}else{stlib.pixels.segmentframe.src=(("https:"==document.location.protocol)?"https://seg.":"http://seg.")+"sharethis.com/getSegment.php?purl="+encodeURIComponent(document.location.href)+"&jsref="+encodeURIComponent(document.referrer)+"&rnd="+(new Date()).getTime();d.appendChild(stlib.pixels.segmentframe)}},pixelOptimizerCallback:function(a){if(typeof(a)!=="undefined"){stlib.data.set("stid",a.stid,"pageInfo");if(a.status=="success"){stlib.pixels.firePixel=true}else{stlib.pixels.firePixel=false}}}};stlib.pixels.createSegmentFrame();return{pixels:stlib.pixels}}})();