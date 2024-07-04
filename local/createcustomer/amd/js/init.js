
    let boaddnew=document.querySelector('#id_bosubmit');
    boaddnew.classList.remove('btn-secondary');
    boaddnew.classList.add('btn-primary');
    let boremove=document.querySelector('#id_boremove');
    boremove.classList.remove('btn-secondary');
    boremove.classList.add('btn-danger');
    let teshortname=document.querySelector('#id_customercode');
    let tename=document.querySelector('#id_customername');
    let selectText=document.querySelector('#id_type');
    let form=document.querySelector('#customerformid');
    let selectContainer=document.querySelector('#fitem_id_type');
    let boaddNewContainer=document.querySelector('#fitem_id_bosubmit');
    let divAfterboAddNew=document.querySelector("#fitem_id_bosubmit>div:nth-child(1)");
    let divAfterboAddNew2=document.querySelector("#fitem_id_bosubmit>div:nth-child(2)");
    let boremoveContainer=document.querySelector('#fitem_id_boremove');
    let divAfterboRemove=document.querySelector("#fitem_id_boremove>div:nth-child(1)");
    let divAfterboRemove2=document.querySelector("#fitem_id_boremove>div:nth-child(2)");

    document.addEventListener('DOMContentLoaded',()=>{
        teshortname.classList.add('form-control');
        tename.classList.add('form-control');
        selectContainer.classList.add('mt-3');
        let boContainer=document.createElement('div');
        boContainer.classList.add('flex');
        boContainer.classList.add('row');
        boContainer.classList.add('m-3');
        boaddNewContainer.classList.remove('form-group');
        boaddNewContainer.classList.remove('row');
        boremoveContainer.classList.remove('form-group');
        boremoveContainer.classList.remove('row');
        boContainer.appendChild(boaddNewContainer);
        boContainer.appendChild(boremoveContainer);
        selectContainer.appendChild(boContainer);
        divAfterboAddNew.remove();
        divAfterboRemove.remove();
        
        divAfterboAddNew2.classList.remove('col-md-9');
        divAfterboAddNew2.classList.remove('form-inline');
        divAfterboAddNew2.classList.remove('align-items-start');
        divAfterboAddNew2.classList.remove('felement');
        divAfterboRemove.classList.remove('col-md-9');
        divAfterboRemove.classList.remove('form-inline');
        divAfterboRemove.classList.remove('align-items-start');
        divAfterboRemove.classList.remove('felement');

        
        
    })

    boremove.addEventListener('click',(e)=>{
        let xhr = new XMLHttpRequest();

        xhr.onload=reqHandlerDropElem;
        xhr.onerror=showError;
        xhr.open('POST','index.php',true);
        const formData= new FormData();
        formData.append('shortname',selectText.value);
        formData.append('operation','delete');

        if (selectText.value==='') {
            alert("Client not selected. Please, select one from the list");
            selectText.focus();
        } else
        //Envio de datos
        xhr.send(formData);
    });

    boaddnew.addEventListener('click',(e)=>{
        let xhr = new XMLHttpRequest();
        if (tename.value===''){
            tename.focus();
            alert("Customer Name can't be empty and must be unique");
            return;
        }
            
        xhr.onload=reqHandlerAddElem;
        xhr.open('POST','index.php',true);
        const formData= new FormData();
        formData.append('shortname',teshortname.value);
        formData.append('name',tename.value);
        formData.append('operation','save');
        //Envio de datos
        xhr.send(formData);
        
    });

    function reqHandlerAddElem(){
        if (this.readyState=== 4 && this. status === 200){
            if ((this.response*1)!==-1){ //If is -1 something went wrong and the customer code wasnt supplied
                
                let option=document.createElement('option');
                option.text=this.response+ ' - ' +teshortname.value + ' - ' + tename.value;
                option.value=teshortname.value;
                selectText.add(option);

            } else {
                alert("Customer Code can't be empty and must be unique");
                teshortname.focus();
            }
        }
    }


    function reqHandlerDropElem(){
        if (this.readyState=== 4 && this. status === 200){
            if ((this.response*1)!==-1){
                selectText.remove(selectText.selectedIndex);

            } else {
                alert("Customer Code can't be empty and must be unique");
                
            }
        }
    }

        function showError(e){
            window.console.log(e);
        }
