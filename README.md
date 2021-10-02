# ci4-indodax-library
Codeigniter4 Indodax API Check Payment BTC Library

# How to use
- Place app/Libraries/Indodax.php in your project.
- Call the Classes example :
<pre>
// Something
use App\Libraries\Indodax;
Class Payment extend BaseController{
	public function index()
	{
		// something
	}
	public function btc_pay(){
		$idx = new Indodax;
		$idx->amount = '';
		$idx->txid = '';
		$data = $idx->run();
		print_r($data);
	}
}
</pre>
