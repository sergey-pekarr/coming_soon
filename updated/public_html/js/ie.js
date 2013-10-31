function getStyleProperty(element, styleProperty)
{
  var prop="";
  if(element.currentStyle)
  {
    prop=element.currentStyle[styleProperty];
  }
  else if(window.getComputedStyle)
  {
    prop=document.defaultView.getComputedStyle(element,null).getPropertyValue(styleProperty);
  }
  return prop;
}

// When the page is loaded
$(function()
{
  // attach PIE on elements with CSS3
  if(window.PIE)
  {
    $("body *").each(function()
    {

     //if (this.className!='btn') //vd(this.className);
     if(getStyleProperty(this,'border-radius')||getStyleProperty(this,'box-shadow')||getStyleProperty(this,'-pie-background'))
     {
//vd(this.className);
       PIE.attach(this);
     }
    });
  }
});