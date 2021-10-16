import React,{useContext} from 'react';
<<<<<<< HEAD
import { Select, FormGroup, InputLabel, MenuItem, Checkbox, FormControlLabel, FormControl, TextField } from '@material-ui/core';
=======
import { Select, FormGroup, InputLabel, MenuItem, Checkbox, FormControlLabel, FormControl } from '@material-ui/core';
>>>>>>> 76d80c5aa49693cf553970b69fc08c8fe8574563
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
                value={ctx.formFields['mem_plan_title']}
                onChange={ctx.changeHandler} 
                id="mem_plan_title" 
                name="mem_plan_title" 
                label="Membership Plan Name"  variant="outlined" className={classes.margin}/>

              <TextField 
                value={ctx.formFields['mem_plan_amount']}
                onChange={ctx.changeHandler} 
                id="mem_plan_amount" 
                name="mem_plan_amount" 
                label="Membership Plan Amount"  variant="outlined" className={classes.margin}/>

              <TextField 
                value={ctx.formFields['mem_plan_product']}
                onChange={ctx.changeHandler} 
                id="mem_plan_product" 
                name="mem_plan_product" 
                label="Include Product in Membership"  variant="outlined" className={classes.margin}/>

            </FormControl>
      
    </>
    )
}
export default SecondStep;