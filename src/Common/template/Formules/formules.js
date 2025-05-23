 /** Formula global function **/

 function initFormula (data) {
           
    return {
         formulas : [] , 
         price : '0' ,
         totalHours : '0' ,
         loadFormulas() {

             fetch('{{ path('tech_student_loadFormulats') }}' + "?module=" + $("#{{sessionModule}}").val() + "&type=" + $("#{{sessionTypeId}}").val()) // Replace with your API endpoint
            .then(response => response.json())
            .then(data => {    this.formulas = data; $("{{formulaId}}").prop("checked", false); this.price = '0' ; this.totalHours = "0" ; 
               
            })
            .catch(error => {    console.error('Error fetching sessions:', error); });

         },
         change () {this.loadFormulas()} ,
         refresh() {
        
            var price =  document.querySelector("{{formulaId}}:checked")?.dataset.price ?? 0
            var hours =  document.querySelector("{{formulaId}}:checked")?.dataset.hours ?? 0
            var additionalHours = $("{{addtionnalHoursId}}").val()
            var total = Number(additionalHours) + Number(hours)
            if(Number(additionalHours)  > 0){
                $("#{{sessionModule}}").val() == 1 ? price = Number(price) + (Number(additionalHours) * 6) :   price = Number(price) + (Number(additionalHours) * 5)
            }
            price = Number(price) + ({% if first is not defined or (first is defined and first == true) %} 5 {% else %} 0 {% endif %})
            this.price = String(price) 
            this.totalHours = String(total)
         }
    }

}
