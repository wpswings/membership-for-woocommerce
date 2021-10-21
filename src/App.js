import {useState} from 'react';
import { makeStyles } from '@material-ui/core/styles';
import { Button, Typography, Container, CircularProgress} from '@material-ui/core';
import Stepper from './component/Stepper';
import SecondStep from './component/SecondStep';
import ThirdStep from './component/ThirdStep';
import FinalStep from './component/FinalStep';
import Context from './store/store';
import axios from 'axios';
import { __ } from '@wordpress/i18n';
import qs from 'qs';
const useStyles = makeStyles((theme) => ({
    instructions: {
        marginTop: theme.spacing(1),
        marginBottom: theme.spacing(1),
    },
}));
function App(props) {
    const [loading, setLoading] = useState(false);
    const [state, setState] = useState({
        firstName: '',
        email: '',
        desc:'',
        licenseCode: '',
        age: '',
        FirstCheckbox:false,
        checkedA: false,
        checkedB: false,
        consetCheck:'yes',
        memPlanAmount:'',
        memPlanTitle:'',
        memPlanProduct:'',        
    });
    
  

jQuery(document).ready(function($) {
    console.log('byee');
    function mwb_mfwp_send_license_request_multi(license_key) {
        debugger;
        $.ajax({
          type: "POST",
          dataType: "JSON",
          url: mfwp_admin_param.ajaxurl,
          data: {
            action: "mfwp_membership_validate_license_key",
            purchase_code: license_key,
          },
    
          success: function(data) {
    console.log(data);
            if (data.status == true) {
              $("#div_licence").css("color", "#42b72a");
              alert(data.msg);
              jQuery("#div_licence").html(data.msg);
    
            //  location = mfwp_admin_param.mfwp_admin_param_location;
            } else {
              $("#div_licence").css("color", "#ff3333");
    
              jQuery("#div_licence").html(data.msg);

             // jQuery("#licenseCode").val("");
            }
          },
        });
      }
      $("#button_licence").on("click", function(e) {
        $("#div_licence").html("");
      });
      $("#button_licence").on("click", function(e) {
        e.preventDefault();
    console.log('hello');
        var license_key = $('#licenseCode').val()
        mwb_mfwp_send_license_request_multi(license_key);
      });
    
    });

    const classes = useStyles();
    const [activeStep, setActiveStep] = useState(0);
    const steps = [  __( 'General Setting', 'membership-for-woocommerce' ), __( 'Membership Creation', 'membership-for-woocommerce' ), __( 'Final Step', 'membership-for-woocommerce' )];

    
    const onFormFieldHandler = (event) => {
        let value = ('checkbox' === event.target.type ) ? event.target.checked : event.target.value;
        setState({ ...state, [event.target.name]: value });
    };
    const getStepContent = (stepIndex) => {
        switch (stepIndex) {
            case 0:
                return <ThirdStep />;
            case 1:
                return (<SecondStep/>);   
           
            case 2:
            return <FinalStep />;
            case 3:
                return <h1>{__( 'Thanks for your details', 'membership-for-woocommerce' )}</h1>;
            default:
                return __( 'Unknown stepIndex', 'membership-for-woocommerce' );
        }
    }
    const handleNext = () => {
        setActiveStep((prevActiveStep) => prevActiveStep + 1);
    };

    const handleBack = () => {
        setActiveStep((prevActiveStep) => prevActiveStep - 1);
    };

    const handleFormSubmit = (e) => {
        e.preventDefault();
        setLoading(true);
        const user = {
            ...state,
            'action': 'mwb_standard_save_settings_filter',
            nonce: frontend_ajax_object.mwb_standard_nonce,   // pass the nonce here
        };
        
        axios.post(frontend_ajax_object.ajaxurl, qs.stringify(user) )
            .then(res => {
                setLoading(false);
                console.log( res.data);
                handleNext();
                setTimeout(() => {
                  window.location.href = frontend_ajax_object.redirect_url; 
                    return null;
                }, 3000);
            }).catch(error=>{
                console.log(error);
        })
        
    }

    let nextButton = (
        <Button
            variant="contained" color="primary" onClick={handleNext} size="large">
            Next
        </Button>
    );
    if (activeStep === steps.length-1 ) {
        nextButton = (
            <Button
                onClick={handleFormSubmit}
                variant="contained" color="primary" size="large">
                Finish
            </Button>
        )
    } 
    return (
        <Context.Provider value={{
            formFields:state,
            changeHandler:  onFormFieldHandler,  
        }}>
            <div className="mwbMsfWrapper">
                <Stepper activeStep={activeStep} steps={steps}/>
                <div className="mwbHeadingWrap">
                    <h2>{__( 'Welcome to Makewebbetter', 'membership-for-woocommerce' ) }</h2>
                    <p>{__('Complete The steps to get started','membership-for-woocommerce') }</p>
                </div>
                <Container maxWidth="sm">
                    <form className="mwbMsf">
                        <Typography className={classes.instructions}>
                            {(loading) ? <CircularProgress className="mwbCircularProgress" /> :getStepContent(activeStep)}
                        </Typography>
                        <div className="mwbButtonWrap">
                            {activeStep !== steps.length && <Button
                                disabled={activeStep === 0}
                                onClick={handleBack}
                                variant="contained" size="large">
                            Back
                            </Button>}
                            {activeStep !== steps.length && nextButton}
                        </div>
                    </form>
                </Container >
            </div>
        </Context.Provider>
    );
}

export default App;

