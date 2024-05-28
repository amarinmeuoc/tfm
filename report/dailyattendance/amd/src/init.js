import {exception as displayException} from 'core/notification';
import Templates from 'core/templates';
export const init = (order,orderby,page, offset, showCustomerSelect,token) => {
    const valuesObj={
        order:order,
        orderby:orderby,
        page:page,
        offset:offset,
        showCustomerSelect:showCustomerSelect,
        token:token,
        pages:document.querySelectorAll('.pagination>li a'),
        customerid:document.querySelector('#customerid'),
        startdate:document.querySelector('#startdate'),
        enddate:document.querySelector('#enddate'),
        group:document.querySelector('#selgroupid'),
        billid:document.querySelector('#billid'),
        attP:document.querySelector('#chkPresent'),
        attA:document.querySelector('#chkAbsent'),
        attL:document.querySelector('#chkLate'),
        attE:document.querySelector('#chkExcused'),
        ordenablefield:document.querySelectorAll('.orderby'),
        button:document.querySelector('#idfilter')
    }

    //Depending of in which column the user clicks, the orderby needs to be setup with the right name.
    valuesObj.ordenablefield.forEach((element) => {
        element.addEventListener('click',(e)=>{
            if (valuesObj.orderby===e.target.dataset.value)
                valuesObj.order=(valuesObj.order==='ASC')?'DESC':'ASC';
            valuesObj.orderby=e.target.dataset.value;
            //valuesObj.page=parseInt(document.querySelector('.pagination .active').textContent);
            valuesObj.page=1;
            processRequest(valuesObj);
        })
    },valuesObj);

    //Controlling the pagination
    valuesObj.pages.forEach(elem=>{
        elem.addEventListener('click',(e)=>{       
            e.preventDefault();
            //page=e.target.closest('li').dataset.value-1;
            processRequest(valuesObj);
        })
    },valuesObj);

    //Filtering...
    valuesObj.button.addEventListener('click',(e)=>{
        valuesObj.page=1; //Starting from page 1
        processRequest(valuesObj);
    },valuesObj)

    if (valuesObj.showCustomerSelect){
        customerid.addEventListener('change',(e)=>{
            updateGroupList(e,valuesObj);
        });
    }
};

const updateGroupList=(e,values)=>{
    const cid=e.target.value;
    requestGroupList(cid,values);
}

const requestGroupList=(cid,values)=>{
    let xhr=new XMLHttpRequest();
    const url='http://'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
 
    const formData= new FormData();
    formData.append('wstoken',values.token);
    formData.append('wsfunction','report_dailyattendance_get_list_group');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][customerid]',cid);

    xhr.send(formData);
    xhr.onload = (event) =>{
        onLoadGroupListFunction(xhr, cid);
    };
    xhr.onprogress = (event)=>{
        onProgressFunction(event);
    } 
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };
}

const onLoadGroupListFunction=(myXhr,cid)=>{
    if (myXhr.readyState===4 && myXhr.status===200){
        const res=JSON.parse(myXhr.response);
        
        //Getting the JSON Object so rebuilding the group Selector
        window.console.log(res);
        const selGroup=document.querySelector('#selgroupid');
        selGroup.innerHTML="";
        res.forEach(group=>{
            selGroup.innerHTML+=`<option value=${group.id}>${group.name}</option>`
        })

        
    }
}

const processRequest=(valuesObj)=>{
    const sdv=Math.floor(new Date(valuesObj.startdate.value).getTime() / 1000);
    const edv=Math.floor(new Date(valuesObj.enddate.value).getTime() / 1000);
    const cv=valuesObj.customerid.value;
    const vv=parseInt(valuesObj.group.value);
    const bv=valuesObj.billid.value;
    const attStatus={
        attPv:(valuesObj.attP.checked)?true:false,
        attAv:(valuesObj.attA.checked)?true:false,
        attLv:(valuesObj.attL.checked)?true:false,
        attEv:(valuesObj.attE.checked)?true:false,
    };
    const ofv=valuesObj.offset;
    const adaptedObj={
        orderby:valuesObj.orderby,
        order:valuesObj.order,
        page:valuesObj.page,
        attStatus:attStatus,
        ofv:ofv,
        sdv:sdv,
        edv:edv,
        cv:cv,
        vv:vv,
        bv:bv,
        showCustomerSelect:valuesObj.showCustomerSelect,
        token:valuesObj.token
    }
    
    processRequestStart(adaptedObj);

    
}

const processRequestStart=(adaptedObj)=>{
    let xhr=new XMLHttpRequest();
    const url='http://'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
 
    const formData= new FormData();
    formData.append('wstoken',adaptedObj.token);
    formData.append('wsfunction','report_dailyattendance_get_list_courses');
    formData.append('moodlewsrestformat','json');
    
    formData.append('params[0][orderby]',adaptedObj.orderby);
    formData.append('params[0][order]',adaptedObj.order);
    formData.append('params[0][page]',adaptedObj.page);
    formData.append('params[0][startdate]',adaptedObj.sdv);
    formData.append('params[0][enddate]',adaptedObj.edv);
    formData.append('params[0][groupid]',adaptedObj.vv);
    formData.append('params[0][billid]',adaptedObj.bv);
    formData.append('params[0][attendanceStatus][statusPresent]',adaptedObj.attStatus.attPv);
    formData.append('params[0][attendanceStatus][statusAbsent]',adaptedObj.attStatus.attAv);
    formData.append('params[0][attendanceStatus][statusLate]',adaptedObj.attStatus.attLv);
    formData.append('params[0][attendanceStatus][statusExcused]',adaptedObj.attStatus.attEv);
    formData.append('params[0][offset]',adaptedObj.ofv);
    formData.append('params[0][customerid]',adaptedObj.cv);
    window.console.log(adaptedObj);
    xhr.send(formData);
    xhr.onload = (event) =>{
        onLoadFunction(xhr, adaptedObj.orderby, adaptedObj.order, adaptedObj.page, adaptedObj.ofv, adaptedObj.showCustomerSelect, adaptedObj.cv,adaptedObj.token);
    };
    xhr.onprogress = (event)=>{
        onProgressFunction(event);
    } 
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };
}

const onLoadFunction=(myXhr, orderby, order, page, ofv, showCustomerSelect, customerid,token)=>{
    const loader=document.querySelector('.loader');
    loader.classList.add('.hide');
    loader.classList.remove('.show');

    if (myXhr.readyState===4 && myXhr.status===200){
        const res=JSON.parse(myXhr.response);
        window.console.log("la pagina seleccionada es: ",page);
        const formattedData={
            showCustomerSelect:showCustomerSelect,
            customerid:customerid,
            attendance_list:res[0].attendance_list,
            num_records:res[0].attendance_list.length,
            num_total_records:res[0].num_total_records,
            order:(res[0].order==='ASC')?true:false,
            orderby:orderby,
            selected_page:page,
            offset:ofv,
            pages:res[0].pages,
            orderbydate:res[0].orderbydate,
            orderbybillid:res[0].orderbybillid,
            orderbygroup:res[0].orderbygroup,
            orderbyname:res[0].orderbyname,
            orderbylastname:res[0].orderbylastname,
            orderbydescription:res[0].orderbydescription,
            orderbywbs:res[0].orderbywbs,
            token:token
        }
        showTemplateAttendance(formattedData);
        window.console.log(res);
    }
}

const onProgressFunction=(event) =>{
    console.log(`Uploaded ${event.loaded} of ${event.total}`);
    const loader=document.querySelector('.loader');
    loader.classList.remove('.hide');
    loader.classList.add('.show');
}

function showTemplateAttendance(response){
    //Render the choosen mustache template by Javascript
    Templates.renderForPromise('report_dailyattendance/content-ajax',response)
    .then(({html,js})=>{
        
        const content=document.querySelector('#content');
        content.innerHTML='';
        Templates.appendNodeContents(content,html,js);
            const pages=document.querySelectorAll('.pagination>li a');
            const customerid=document.querySelector('#customerid');
            const sd=document.querySelector('#startdate');
            const ed=document.querySelector('#enddate');
            const group=document.querySelector('#selgroupid');
            const billid=document.querySelector('#billid');
            const attP=document.querySelector('#chkPresent');
            const attA=document.querySelector('#chkAbsent');
            const attL=document.querySelector('#chkLate');
            const attE=document.querySelector('#chkExcused');
            const ordenablefield=document.querySelectorAll('.orderby');
            const order=(response.order)?'ASC':'DESC';
            const showCustomerSelect=response.showCustomerSelect;
            const offset=response.offset;
            const orderby=response.orderby;
            const page=response.selected_page;
            const token=response.token;

            const valuesObj={
                order:order,
                orderby:orderby,
                page:page,
                offset:offset,
                showCustomerSelect:showCustomerSelect,
                token:token,
                pages:pages,
                customerid:customerid,
                startdate:sd,
                enddate:ed,
                group:group,
                billid:billid,
                attP:attP,
                attA:attA,
                attL:attL,
                attE:attE,
                ordenablefield:ordenablefield
            }
            
           

            //Depending of in which column the user clicks, the orderby needs to be setup with the right name.
            ordenablefield.forEach((element) => {
                element.addEventListener('click',(e)=>{
                    e.preventDefault();
                    //const page=parseInt(document.querySelector('.pagination .active').textContent);
                    const page=1;
                    window.console.log("Pagina seleccionada al ordenar es: ",page);
                    if (valuesObj.orderby===e.target.dataset.value)
                        valuesObj.order=(valuesObj.order==='ASC')?'DESC':'ASC';
                    const orderby=e.target.dataset.value;
                    valuesObj.orderby=orderby;
                    valuesObj.page=page;
                    processRequest(valuesObj);
                })
            },valuesObj);
            //Controlling the pagination
            pages.forEach((elem)=>{

                elem.addEventListener('click',(e)=>{
                    const pages=document.querySelectorAll('.pagination>li');
                    pages.forEach(elem=>{
                        elem.classList.remove('active');
                    });
                    
                    e.preventDefault();
                    pageval=parseInt(e.target.text);
                    window.console.log("Pagina seleccionada al paginar es: ",page);
                    valuesObj.page=pageval;
                    processRequest(valuesObj);
                })
                
            },valuesObj);

    })
    .catch((error)=>displayException(error));
  }