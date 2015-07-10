<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
//https://gist.github.com/bateller/154c6e5d1f6e0e53e527
require_once('vendor/autoload.php');

class stripe
{

    private $ci;
    public $config;

    public function __construct()
    {
        $this->ci = & get_instance();

        $this->config = $this->ci->config->config['stripe'];

        $mode = $this->config['mode'];

        $this->config['secret_key'] = $this->config['sk_'.$mode];

        $this->config['publishable_key'] = $this->config['pk_'.$mode];

        \Stripe\Stripe::setApiKey($this->config['secret_key']);
    }
    //array( amount, 
    //       customer, 
    //       source(Stripe.js), 
    //       description, 
    //       metadata, 
    //       capture(Whether or not to immediately capture the charge. When false, the charge issues an authorization),
    //       statement_descriptor,
    //       receipt_email,
    //       destination (CONNECT ONLY, An account to make the charge on behalf of),
    //       application_fee (CONNECT ONLY, A fee in cents that will be applied to the charge and transferred to the application owner's Stripe account.),
    //       shipping default is { } )
    public function addCharge($options = [])
    {
       $options["currency"] = $this->config['currency'];
       return  \Stripe\Charge::create($options);
    }

    public function getCharge($charge_id)
    {
       return \Stripe\Charge::retrieve($charge_id);
    }

    //This accepts only the description, metadata, receipt_emailand fraud_details as arguments(key).
    public function updateCharge($charge_id, $options)
    {
        $ch = $this->getCharge($charge_id);
        foreach ($options as $key => $value) {
            $ch->$key = $value;
        }
        return $ch->save();
    }

    public function captureCharge($charge_id)
    {
        return $this->getCharge($charge_id)->capture();
    }

    // 'created'
    // optional associative array
    // A filter on the list based on the object created field. The value can be a string with an integer Unix timestamp, or it can be a dictionary with the following options:
    //  child arguments
            // 'gt'
            // optional
            // Return values where the created field is after this timestamp.
            // 'gte'
            // optional
            // Return values where the created field is after or equal to this timestamp.
            // 'lt'
            // optional
            // Return values where the created field is before this timestamp.
            // 'lte'
            // optional
            // Return values where the created field is before or equal to this timestamp.
    // 'customer'
    // optional
    // Only return charges for the customer specified by this customer ID.
    // 'ending_before'
    // optional
    // A cursor for use in pagination. ending_before is an object ID that defines your place in the list. For instance, if you make a list request and receive 100 objects, starting with obj_bar, your subsequent call can include ending_before=obj_bar in order to fetch the previous page of the list.
    // 'limit'
    // optional, default is 10
    // A limit on the number of objects to be returned. Limit can range between 1 and 100 items.
    // 'starting_after'
    // optional
    // A cursor for use in pagination. starting_after is an object ID that defines your place in the list. For instance, if you make a list request and receive 100 objects, ending with obj_foo, your subsequent call can include starting_after=obj_foo in order to fetch the next page of the list.
    
    public function listCharges($options=[])
    {
        return \Stripe\Charge::all($options);
    }

    public function addRefund($charge_id)
    {
        return $this->getCharge($charge_id)->refunds->create();
    }

    public function getRefund($charge_id, $refund_id)
    {
        return $this->getCharge($charge_id)->refunds->retrieve($refund_id);
    }
    //This request only accepts metadata as an argument.
    public function updateRefund($charge_id, $refund_id, $options)
    {
        $re = $this->getRefund($charge_id,$refund_id);
        foreach ($options as $key => $value) {
            $re->$key = $value;
        }
        return $re->save();
    }

    //array (ending_before, limit, starting_after)
     public function listRefunds($charge_id, $options=[])
    {
        return $this->getRefund($charge_id)->refunds->all($options);
    }
    // array (account_balance, 
    //        coupon, 
    //        description, 
    //        email, 
    //        metadata, 
    //        plan, 
    //        quantity(plans), 
    //        source (Stripe.js), 
    //        tax_percent, 
    //        trial_end ) 
    public function addCustomer($options=[])
    {
        return \Stripe\Customer::create();
    }

    public function getCustomer($customer_id)
    {
       return \Stripe\Customer::retrieve($customer_id);
    }

    public function updateCustomer($customer_id, $key, $options)
    {
        $cu = $this->getCustomer($customer_id);
        foreach ($options as $key => $value) {
            $cu->$key = $value;
        }
        return $cu->save();
    }

    public function deleteCustomer($customer_id)
    {
        $cu = $this->getCustomer($customer_id);
        return $cu->delete();
    }

    // 'created'
    // optional associative array
    // A filter on the list based on the object created field. The value can be a string with an integer Unix timestamp, or it can be a dictionary with the following options:
    //  child arguments
            // 'gt'
            // optional
            // Return values where the created field is after this timestamp.
            // 'gte'
            // optional
            // Return values where the created field is after or equal to this timestamp.
            // 'lt'
            // optional
            // Return values where the created field is before this timestamp.
            // 'lte'
            // optional
            // Return values where the created field is before or equal to this timestamp.
    
    // 'ending_before'
    // optional
    // A cursor for use in pagination. ending_before is an object ID that defines your place in the list. For instance, if you make a list request and receive 100 objects, starting with obj_bar, your subsequent call can include ending_before=obj_bar in order to fetch the previous page of the list.
    // 'limit'
    // optional, default is 10
    // A limit on the number of objects to be returned. Limit can range between 1 and 100 items.
    // 'starting_after'
    // optional
    // A cursor for use in pagination. starting_after is an object ID that defines your place in the list. For instance, if you make a list request and receive 100 objects, ending with obj_foo, your subsequent call can include starting_after=obj_foo in order to fetch the next page of the list.
    public function listCustomers($opctions=[])
    {
        return \Stripe\Customer::all($opctions);
    }
    //$options array(plan(REQUIRED), 
    //               coupon, 
    //               trial_end, 
    //               source(Stripe.js), 
    //               quantity, 
    //               application_fee_percent, 
    //               tax_percent, 
    //               metadata ) 
    public function addSubscription($customer_id, $options)
    {
        return $this->getCustomer($customer_id)->subscriptions->create($options);
    }

    public function getSubscription($customer_id, $subscription_id)
    {
        return $this->getCustomer($customer_id)->subscriptions->retrieve($subscription_id);
    }
    //$options array(plan, 
    //               prorate, 
    //               proration_date(default is the current time), 
    //               coupon, trial_end, 
    //               source(Stripe.js), 
    //               quantity, 
    //               application_fee_percent, 
    //               tax_percent, metadata )
    public function updateSubscription($customer_id, $subscription_id, $options)
    {
        $su = $this->getSubscription($customer_id, $subscription_id);
        foreach ($options as $key => $value) {
            $su->$key = $value;
        }
        
        return $su->save();
    }

    public function cancelSubscription($customer_id, $subscription_id)
    {

        return $this->getSubscription($customer_id, $subscription_id)->cancel();
    }
}
