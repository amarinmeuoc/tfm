import {exception as displayException} from 'core/notification';
import Templates from 'core/templates';

export const init = (customerid,order,orderby,orderbystartdate,orderbyenddate) => {
    let bofilter=document.querySelector('#bofilter');
    let sdlink=document.querySelector('#sd-link');
    let edlink=document.querySelector('#ed-link');

    bofilter.addEventListener('click',()=>{
        let day=document.querySelector('#id_assesstimefinish_day').value;
        let month=document.querySelector('#id_assesstimefinish_month').value;
        let year=document.querySelector('#id_assesstimefinish_year').value;
        let group=document.querySelector('#selgroupid').value;
        let billid=document.querySelector('#tebillid').value;
        let date=new Date(year,month-1,day);
        let unixtime=date.getTime()/1000;
        
        requestResultFromServer(customerid,group,billid,unixtime,order,orderby,orderbystartdate,orderbyenddate);
    });

    sdlink.addEventListener('click',(e)=>{
        e.preventDefault();
        let day=document.querySelector('#id_assesstimefinish_day').value;
        let month=document.querySelector('#id_assesstimefinish_month').value;
        let year=document.querySelector('#id_assesstimefinish_year').value;
        let group=document.querySelector('#selgroupid').value;
        let billid=document.querySelector('#tebillid').value;
        let date=new Date(year,month-1,day);
        let unixtime=date.getTime()/1000;
        orderbystartdate=true;
        orderbyenddate=false;
        orderby='startdate';
        order=order?false:true;
        
        requestResultFromServer(customerid,group,billid,unixtime,order,orderby,orderbystartdate,orderbyenddate);
    });

    edlink.addEventListener('click',(e)=>{
        e.preventDefault();
        let day=document.querySelector('#id_assesstimefinish_day').value;
        let month=document.querySelector('#id_assesstimefinish_month').value;
        let year=document.querySelector('#id_assesstimefinish_year').value;
        let group=document.querySelector('#selgroupid').value;
        let billid=document.querySelector('#tebillid').value;
        let date=new Date(year,month-1,day);
        let unixtime=date.getTime()/1000;
        orderbyenddate=true;
        orderbystartdate=true;
        orderby='enddate';
        order=order?false:true;
        
        requestResultFromServer(customerid,group,billid,unixtime,order,orderby,orderbystartdate,orderbyenddate);
    });
    
}

const requestResultFromServer=(customerid,group,billid,unixtime,order,orderby,orderbystartdate,orderbyenddate)=>{
    let xhr=new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    const token=document.querySelector('#id_token').value;
    xhr.open('POST',url,true);
    const formData=new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','report_partialplan_get_training_plan');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][customerid]',customerid);
    formData.append('params[0][group]',parseInt(group));
    formData.append('params[0][billid]',billid);
    formData.append('params[0][unixtime]',unixtime);
    formData.append('params[0][order]',(order)?1:0);
    formData.append('params[0][orderby]',orderby);
    formData.append('params[0][orderbystartdate]',(orderbystartdate)?1:0);
    formData.append('params[0][orderbyenddate]',(orderbyenddate)?1:0);
    xhr.send(formData);
    xhr.onload = (event) =>{
        onLoadFunction(xhr,group,token);
        };
    xhr.onprogress = (event)=>{
        onProgressFunction(event);
    } 
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };
}

const onLoadFunction=(myXhr,selectedGroup,token)=>{
    if (myXhr.readyState===4 && myXhr.status===200){
        const res=JSON.parse(myXhr.response);
        const formattedData={
            token:token,
            customerid:res[0].customerid,
            group:res[0].group,
            selectedGroup:selectedGroup,
            courses:res[0].courses,
            orderbystartdate:(res[0].orderbystartdate),
            orderbyenddate:(res[0].orderbyenddate),
            orderby:res[0].orderby,
            order:(res[0].order)
        }
        window.console.log(formattedData);
        showTemplateAssessment(formattedData);
    }
}

const onProgressFunction= (event)=>{
    if (event.lengthComputable) {
      window.console.log(`Recibidos ${event.loaded} de ${event.total} bytes`);
    } else {
      window.console.log(`Recibidos ${event.loaded} bytes`); // sin Content-Length
    }
}

function showTemplateAssessment(response){
    

    //Render the choosen mustache template by Javascript
    Templates.renderForPromise('report_partialplan/content',response)
    .then(({html,js})=>{
    const content=document.querySelector('#content');
    content.innerHTML='';
      Templates.appendNodeContents(content,html,js);
      const selectGroup=document.querySelector('#selgroupid');
      const desiredValue = response.selectedGroup;

      let optionIndex=0;
    for (let i = 0; i < selectGroup.options.length; i++) {
        if (selectGroup.options[i].value === desiredValue) {
            optionIndex = i;
            window.console.log(optionIndex);
            break;
        }
    }

    selectGroup.selelectedIndex=optionIndex;
    selectGroup.value=desiredValue;

    })
    .catch((error)=>displayException(error));
  }