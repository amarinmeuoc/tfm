import {exception as displayException} from 'core/notification';
import Templates from 'core/templates';
export const loadTemplate = () => {
    requestDataToServer();
    
    const bosubmit=document.querySelector('#id_bosubmit');
    
    bosubmit.addEventListener('click',()=>{
      requestDataToServer();
    })
}

const requestDataToServer= ()=>{
    const customerSel=document.querySelector('#id_customer');
    if (customerSel===null)
        customerid=-1;
    else
        customerid=customerSel.value;
    const role=document.querySelector('#id_role');
    const groupSel=document.querySelector('#id_grouptrainee');
    const selListTrainees=document.querySelector('#id_list_trainees');
    const selListCourses=document.querySelector('#id_list_courses');
    const startdate_day=document.querySelector('#id_startdate_day');
    const startdate_month=document.querySelector('#id_startdate_month');
    const startdate_year=document.querySelector('#id_startdate_year');
    const startdate=Math.floor(new Date(startdate_year.value+'.'+startdate_month.value+'.'+startdate_day.value).getTime()/1000);
    const queryType=document.querySelector('input[type="radio"][name="status"]:checked');
    
   
    const token=document.querySelector('input[name="token"]').value;
    const xhr=new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
    //const roleValue=(role.value===1)?true:false;
    const formData=new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','report_coursereport_get_assessment');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][role]',role.value);
    formData.append('params[0][customerid]',customerid);
    formData.append('params[0][groupid]',groupSel.value);
    formData.append('params[0][billid]',selListTrainees.value);
    formData.append('params[0][wbs]',selListCourses.value);
    formData.append('params[0][startdate]',startdate);
    formData.append('params[0][offset]',100);
    formData.append('params[0][order]',1);
    formData.append('params[0][orderby]','shortname');
    formData.append('params[0][queryType]',queryType.value);
    formData.append('params[0][page]',1);

    

    xhr.send(formData);
    xhr.onload = (event) =>{
        onLoadFunction(xhr);
        };
      xhr.onprogress = (event)=>{
        onProgressFunction(event);
      } 
      xhr.onerror = function() {
        window.console.log("Solicitud fallida");
      };
}

const onLoadFunction=(myXhr)=>{
    if (myXhr.readyState=== 4 && myXhr. status === 200){
        const res=JSON.parse(myXhr.response);
        res[0].assessment_list=res[0].assessment_list.map(obj=>{
          obj.assessment=obj.assessment*1;
          obj.assessment= obj.assessment.toFixed(2);
          obj.attendance=obj.attendance*1;
          obj.attendance= obj.attendance.toFixed(2);
          return obj;
        })
        showTemplateAssessment(res[0]);
        
    }
}

function showTemplateAssessment(response){
    //Render the choosen mustache template by Javascript
    Templates.renderForPromise('report_coursereport/content-ajax',response)
    .then(({html,js})=>{
    const content=document.querySelector('#content');
    content.innerHTML='';
      Templates.appendNodeContents(content,html,js);
    })
    .catch((error)=>displayException(error));
  }