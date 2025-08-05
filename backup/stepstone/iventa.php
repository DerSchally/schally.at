<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title>Convert characters to Unicode</title>
   <meta http-equiv="Content-Type"
         content="text/css; charset=utf-8" />
   <style type="text/css"
          media="all">
   </style>
   <meta name="ROBOTS"
         content="ALL" />
   <meta name="Copyright"
         content="Copyright M. Swofford" />
   <meta http-equiv="imagetoolbar"
         content="no" />
   <meta name="MSSmartTagsPreventParsing"
         content="true" />
   <meta name="author"
         content="Mark Swofford" />

   <meta name="keywords"
         content="character converter, unicode converter" />
   <style type="text/css">
   /*<![CDATA[*/
   <!--
   h3      {
        border-top: 2px solid green;
	margin: .5em 0 0 0;
	padding: .5em 0 0 0;
        }
   .code   {
        font-family: monospace;
        color: green;
        font-weight: bold;
        }
   textarea        {
        width: 500px;
	}
form	{
	margin: 0;
	padding: .3em 0;
        }
   -->
   /*]]>*/
   </style>
   <script type="text/javascript">
   
   function convertToEntities() {
  var tstr = document.form.unicode.value;
  var bstr = '';
  for(i=0; i<tstr.length; i++)
  {
    if(tstr.charCodeAt(i)>127)
    {
      bstr += '&#' + tstr.charCodeAt(i) + ';';
    }
    else
    {
      bstr += tstr.charAt(i);
    }
  }
  document.form.entity.value = bstr;
}

</script>
   
   
   //<![CDATA[

   //]]>
   </script>
   <script type="text/javascript">
   //<![CDATA[
                window.onload=function(){convertToEntities();} 
   //]]>
   </script>
</head>

<body>
   <div id="container">

           
      <div id="main">
         <h1>Convert Chinese characters to Unicode</h1>
         <h3>Input your text:</h3>
         <form method="post" action="chars2uninumbers.html" name="form">
         <textarea name="unicode" rows="5"></textarea><br />
             

		<input type="button"
                  name="convert"
                  value="convert"
                  onclick="convertToEntities()" /> 
             <input type="reset"
                  name="clear"
                  value="clear results" /> <br />

            <h3>Results:</h3>
            <textarea
 name="entity"
                  rows="10"
                  readonly="readonly"
                  id="output">
</textarea>
         </form>



</div>
</body>
</html>