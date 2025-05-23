$("#arabe").click(function(){
    $(this).addClass("selected")
    $("#coran").removeClass("selected")
    $("#inputSessionModule").val(1)
})
$("#coran").click(function(){
    $(this).addClass("selected")
    $("#arabe").removeClass("selected")
    $("#inputSessionModule").val(2)
})
var types = [1,2,3] ;

types.map((e)=> {
       $("#type"+e).click(function(){
        $(this).addClass("selected")
        $("#inputSessionType").val(e)
        types.map((el)=> {
            if(el != e){
                $("#type"+el).removeClass("selected")
            }
        })
        e == 2 ? $("#binomeMode").show() : $("#binomeMode").hide()
})})


 // Step 1 button
 $("#save1").on("click",(e)=> {

    if($("#inputSessionModule").val() ==""){
        $("#t1").css("color","red")
        return false
    }
     
    
    if($("#inputSessionType").val() ==""){
        $("#t2").css("color","red")
        return false
    }

    if($("input[name='session[hasCollaborator]']:checked").length == 0 && $("#inputSessionType").val() == '2'){
        $("#t3").css("color","red")
        return false
    }

    $("#step2-tab").attr("disabled",false).click()
   
})


// Step 2 button
$("#save2").on("click",(e)=> {

    if($("#days").val().length == 0){
        $("#t3").css("color","red")
        return false
    }
     
    
    if($("input[name='session[availability][]']:checked").length == 0){
        $("#t4").css("color","red")
        return false
    }

    if($("#session\\[timezone\\]").val() == ""){
        $("#t5").css("color","red")
        return false
    }


    $("#step4-tab").attr("disabled",false).click()
   
})


// Step 3 button
$("#save3").on("click",(e)=> {
    
    if($("input[name='session[formula]']:checked").length == 0){
        $("#t6").css("color","red")
        return false
    }

    $("#step3-tab").attr("disabled",false).click()
    $("#daysRequired").text($("input[name='session[formula]']:checked").data("days"))
    $("#days").trigger("change")

})



$("#coran,#arabe").click((e)=> $("#t1").css("color","#000") )
$("#type1,#type2,#type3").click((e)=> $("#t2").css("color","#000") )
$("#type2").click((e)=> $("#infoBinome").show())
$("#type3").click((e)=> $("#infoGroup").show())
$("#type1,#type2").click((e)=> $("#infoGroup").hide())
$("#type1,#type3").click((e)=> $("#infoBinome").hide())
$("#existant").click((e)=> $("#maxStudent").hide())
$("#perso").click((e)=> $("#maxStudent").show())

$("#days").change((e)=> {

    if ($("#days option:selected").length < $("input[name='session[formula]']:checked").data("days") && $("#days option:selected").filter(function() { return $(this).val() === "All"; }).length == 0) {
        $("#save2").attr("disabled",true)
    }else{
        $("#save2").attr("disabled",false)
    }    

})


setTimeout((e)=> $("input[name='session[formula]']").change((e)=> $("#t6").css("color","#000") ) ,5000)


 getTimeZones()

async function getTimeZones () {
    const response = await fetch("https://api.timezonedb.com/v2.1/list-time-zone?key=JH7ZZEK2X9RJ&format=json&");
    const data = await response.json();
    if (data.status === 'OK') {
        data.zones.forEach(zone => {
            const countryName = zone.countryName;
            const gmtOffset = zone.gmtOffset / 3600; // Convert seconds to hours
            $("#session\\[timezone\\]").append(`<option ${zone.countryName == "France" ? "selected" : ""} value='${countryName} GMT + ${gmtOffset}' > ${countryName} GMT + ${gmtOffset} </option>`)
        });
        $("#session\\[timezone\\]").select2()
    } else {
        console.error('Error fetching data:', data.message);
    }
}

