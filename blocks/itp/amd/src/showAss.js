import {exception as displayException} from 'core/notification';
import Templates from 'core/templates';
export const init = (token,email,firstname,lastname) => {
    let linkass=document.querySelectorAll('.linkass');
    let customer,group,billid;

    //Show the assessment in detail
    const showAssessmentList=(event)=>{
      showAssessmentListFunction(event,token,email,firstname,lastname);
    };

    if (linkass.length>0 && linkass!==null){
      customer=linkass[0].attributes.customer.value;
      group=linkass[0].attributes.group.value;
      billid=linkass[0].attributes.billid.value;

      linkass.forEach((link)=>{
        link.addEventListener('click',showAssessmentList);
      });
    } else  {
      customer=null;
      group=null;
      billid=null;
    }
};

const showAssessmentListFunction=(e,token,email,firstname,lastname)=>{

  e.preventDefault();
  let courseid=e.target.attributes.courseid.value;
  const xhr=new XMLHttpRequest();
  const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
  xhr.open('POST',url,true);
  const formData= new FormData();
  formData.append('wstoken',token);
  formData.append('wsfunction','block_itp_get_assessment_details');
  formData.append('moodlewsrestformat','json');
  formData.append('params[0][courseid]',courseid);
  formData.append('params[0][email]',email);

  xhr.send(formData);
  xhr.onload = (event) =>{
    onLoadFunction(xhr,firstname,lastname,token);
    };
  xhr.onprogress = (event)=>{
    onProgressFunction(event);
  } 
  xhr.onerror = function() {
    window.console.log("Solicitud fallida");
  };
}

const onLoadFunction=(myXhr,firstname,lastname,token)=>{
  if (myXhr.readyState=== 4 && myXhr. status === 200){
    const res=JSON.parse(myXhr.response);
    const formattedData = {
      token:token,
      customer:customer,
      group:group,
      billid:billid,
      user: {
        firstname:firstname,
        lastname:lastname
      },
      assessmentDetails: res.map(item => {
        // Customize object properties for the template as needed
        return {
          // Example:
          kpi: item.kpi,
          id: item.id,
          coursename:item.coursename,
          type:item.type,
          score:item.score,
          feedback: item.feedback,
          timemodified:item.timemodified
          // ... add other properties
        };
      })
    };
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
  Templates.renderForPromise('block_itp/assessmentDetails',response)
  .then(({html,js})=>{
    const content=document.querySelector('.block_itp_content');
    content.innerHTML='';
    window.scrollTo(0,0);
    Templates.appendNodeContents('.block_itp_content',html,js);
    
  })
  .catch((error)=>displayException(error));
}