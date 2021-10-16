import React,{useContext} from 'react';
import { Select, FormGroup, InputLabel, MenuItem, Checkbox, FormControlLabel, FormControl, TextField } from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import { __ } from '@wordpress/i18n';
import Context from '../store/store';
const useStyles = makeStyles({
      margin: {
        marginBottom: '20px',
      },
});
const SecondStep = (props) => {
    const classes = useStyles();
    const ctx = useContext(Context);
    return ( 
    <>
          <h3 className="mwb-title">{ __( 'Membership plan Creation', 'membership-for-woocommerce' ) }</h3>
            <FormControl component="fieldset" fullWidth className="fieldsetWrapper">

              <TextField 
                value={ctx.formFields['memPlanTitle']}
                onChange={ctx.changeHandler} 
                id="memPlanTitle" 
                name="memPlanTitle" 
                label="Membership Plan Name"  variant="outlined" className={classes.margin}/>

              <TextField 
                value={ctx.formFields['memPlanAmount']}
                onChange={ctx.changeHandler} 
                id="memPlanAmount" 
                name="memPlanAmount" 
                label="Membership Plan Amount"  variant="outlined" className={classes.margin}/>

              <TextField 
                value={ctx.formFields['memPlanProduct']}
                onChange={ctx.changeHandler} 
                id="memPlanProduct" 
                name="memPlanProduct" 
                label="Include Product in Membership"  variant="outlined" className={classes.margin}/>

            </FormControl>
      
    </>
    )
}
export default SecondStep;