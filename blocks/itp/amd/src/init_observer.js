export const init=(token,customercode)=>{
    const bosubmit=document.querySelector('#fitem_id_bosubmit');
    
    const groupSelect=document.querySelector('#id_selgroup');
    const selListTrainees=document.querySelector('#id_list_trainees');

    bosubmit.classList.remove('row');
    isElementLoaded('#fitem_id_list_trainees>div>div>span[role="option"]').then((selector)=>{
        removeOptions(selListTrainees);
    
        updateListTrainees(customercode,groupSelect,selListTrainees,token);
    
        groupSelect.addEventListener('change',(ev)=>{
            updateListTrainees(customercode,groupSelect,selListTrainees,token);
        },token,customercode)
    });
    


function reloadGroup(){
    if (this.readyState=== 4 && this. status === 200){
        if (this.response){
            const list_group=JSON.parse(this.response);
            
            removeOptions(groupSelect);

            list_group.map(obj=>{
                const opt=document.createElement('option');
                opt.value=obj.id;
                opt.textContent=obj.name;
                groupSelect.appendChild(opt);
            });
            updateListTrainees(customercode,groupSelect,selListTrainees,token)
        } 
    }
}


}

const updateListTrainees=(customercode,groupSelect,listTrainees,token)=>{
    let xhr = new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    const selectedCustomer=customercode;
    const selectedGroup=groupSelect.options[groupSelect.selectedIndex].textContent;
    xhr.open('POST',url,true);
    const formData= new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','block_itp_get_trainee_list');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][customercode]',selectedCustomer);
    formData.append('params[0][groupname]',selectedGroup);
    xhr.send(formData);
    xhr.onload = ()=>{
        reloadTraineeList(xhr,listTrainees)
    };

    xhr.onprogress = (event)=>{
        onProgressFunction(event,listTrainees);
      } 
    
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };
}

const onProgressFunction=(event,listTrainees)=>{
    //removeOptions(listTrainees);
}

const reloadTraineeList=(xhr,listTrainees)=>{
    
    if (xhr.readyState=== 4 && xhr. status === 200){
        if (xhr.response){
            const list_trainees=JSON.parse(xhr.response);
            
            let selectedValue=document.querySelectorAll('dd')[1].textContent.trim();
            let selectedTrainee=list_trainees.filter(obj=>obj.billid===selectedValue)[0];
            if (selectedTrainee===undefined){
                if (list_trainees.length>0){
                    selectedTrainee=list_trainees[0];
                    selectedValue=selectedTrainee.billid;
                } else {
                    selectedTrainee='';
                    selectedValue='';
                } 
            }

            
            
            const option=document.createElement('option');
            option.value=selectedValue;
            option.textContent=selectedTrainee.groupname+"_"+selectedTrainee.billid+" "+selectedTrainee.firstname+", "+selectedTrainee.lastname;
            removeOptions(listTrainees);
            listTrainees.appendChild(option);

            list_trainees.map(obj=>{
                const opt=document.createElement('option');
                opt.value=obj.billid;
                opt.textContent=obj.groupname+"_"+obj.billid+" "+obj.firstname+", "+obj.lastname;
                listTrainees.appendChild(opt);
            })

            let activeSpan = document.querySelector('#fitem_id_list_trainees>div>div>span[role="option"]');
            if (activeSpan!==null){
                activeSpan.setAttribute('data-value',(list_trainees.length===0)?'':list_trainees[0].billid);
                const span=document.createElement('span');
                span.setAttribute('aria-hidden',true);
                span.textContent="× "
                activeSpan.innerHTML="";
                activeSpan.appendChild(span);
                activeSpan.innerHTML+= selectedTrainee.groupname+"_"+selectedTrainee.billid+" "+selectedTrainee.firstname+", "+selectedTrainee.lastname;
            } 
        } 
    }
}

const removeOptions=(selectElement) =>{
    let i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
       selectElement.remove(i);
    }
 }

 const isElementLoaded = async selector => {
    while ( document.querySelector(selector) === null) {
      await new Promise( resolve =>  requestAnimationFrame(resolve) )
    }
    return document.querySelector(selector);
  };