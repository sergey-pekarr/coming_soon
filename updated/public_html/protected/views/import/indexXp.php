<div id="count">0</div>

<a href="javascript:startImport()">Start</a>
<br />

<a href="/import/deletePromosXP">DELETE ALL XP PROMOS</a>

<script src="/js/jquery_min.js"></script>


<script type="text/javascript">
    
    var count = 0;
    
    function startImport()
    {
        $.post("/import/XP", {ajax:true} , function(data){
            if(data != undefined)
            {
                count += data.count; 
                $("#count").html(count);
                //$("#count").after(data.time_find+' / '+data.time_load + '<br />');
                
                
                if ( data.count!=0 && count<1000)
                {
                    startImport();
                }               
                 
            };
        }, "json");  
    }
</script>
