//micro lib
function trace(a){if(typeof console!=="undefined")console.log(a);}
function clearInput(t){if(t.value==t.defaultValue)t.value="";}
function resetInput(t){if(t.value=="")t.value=t.defaultValue;}
function xmlToString(d){var s;if(window.ActiveXObject){s=d.xml;}else{s=(new XMLSerializer()).serializeToString(d);}return s;}
function jsonToString(d){return JSON.stringify(d);}
function validateEmail(e){var a=e.indexOf("@");var d=e.lastIndexOf(".");if (a<1||d<a+2||d+2>=e.length)return false;return true;}
function changeColorFocus(o){o.style.color="#262626";}
function changeColorBlur(o){if(o.value==o.defaultValue||o.value=="")o.style.color="#a8a8a8";}
function startLoading(){$("#loading").css({display:"table"});}
function stopLoading(){$("#loading").hide();}
function getScrollOffsets(w){w=w||window;if(w.pageXOffset!=null)return{x:w.pageXOffset,y:w.pageYOffset};var d=w.document;if(document.compatMode=="CSS1Compat"){return{x:d.documentElement.scrollLeft,y:d.documentElement.scrollTop};}return{x:d.body.scrollLeft,y:d.body.scrollTop};}
function scrollToTop(){$('html, body').animate({scrollTop:0},300);}
function excerpt(s,l){if (s.length>l)s=s.substr(0,s.lastIndexOf(' ',l))+'...';return s;}
function isIeLowerThan(m){if(document.documentMode){if(document.documentMode>=m)return false;return true;}else{for(var i=7;i>0;i--){var d=document.createElement("div");d.innerHTML="<!--[if IE "+i+"]><span></span><![endif]-->";if(d.getElementsByTagName("span").length){if(i>=m)return false;return true;}}}return false;}
function showPasswordInput(t,id){t.style.display='none';var p=document.getElementById(id);p.style.display='inline';p.focus();}
function isUndefined(o){if(typeof o!=='undefined')return false;return true;}
function zeroPad(num,places){var z=places-num.toString().length+1;return Array(+(z > 0 && z)).join("0")+num;}

var QueryString = function () {//get url param
  // This function is anonymous, is executed immediately and the return value is assigned to QueryString!
  var query_string = {};
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++) {
    var pair = vars[i].split("=");
        // If first entry with this name
    if (typeof query_string[pair[0]] === "undefined") {
      query_string[pair[0]] = decodeURIComponent(pair[1]);
        // If second entry with this name
    } else if (typeof query_string[pair[0]] === "string") {
      var arr = [ query_string[pair[0]],decodeURIComponent(pair[1]) ];
      query_string[pair[0]] = arr;
        // If third or later entry with this name
    } else {
      query_string[pair[0]].push(decodeURIComponent(pair[1]));
    }
  } 
  return query_string;
}();

var _sw = 1000;
var _sh = 500;
var _clickOrTouch = (('ontouchend' in window)) ? 'touchend' : 'click';//remove 300ms delay on ios