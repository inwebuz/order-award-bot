<?php

namespace App\Http\Controllers;

use App\Category;
use App\Helpers\BotHelper;
use App\Helpers\Helper;
use App\Order;
use App\Product;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    private $page = '/orders';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $this->getFilter($request);
        $query = $this->generateQuery($request);

        $queryClone = clone $query;
        $orders = $query->paginate(10);

        $ordersAll = $queryClone->select('id', 'total', 'status')->get();

        $stats = [
            'all' => [
                'quantity' => $ordersAll->count(),
                'sum' => $ordersAll->sum('total'),
            ],
            'open' => [
                'quantity' => $ordersAll->where('status', Order::STATUS_OPEN)->count(),
                'sum' => $ordersAll->where('status', Order::STATUS_OPEN)->sum('total'),
            ],
            'close' => [
                'quantity' => $ordersAll->where('status', Order::STATUS_CLOSE)->count(),
                'sum' => $ordersAll->where('status', Order::STATUS_CLOSE)->sum('total'),
            ],
        ];

        return view('orders.index', compact('orders', 'filter', 'stats'));
    }

    public function download(Request $request)
    {
        $filter = $this->getFilter($request);
        $query = $this->generateQuery($request);
        $ordersAll = $query->get();

        $writer = WriterEntityFactory::createXLSXWriter();
        // $writer = WriterEntityFactory::createODSWriter();
        // $writer = WriterEntityFactory::createCSVWriter();

        $fileName = 'Orders-' . $filter['period_from']->format('d.m.Y') . '-' . $filter['period_to']->format('d.m.Y') . '.xlsx';
        // $writer->openToFile($filePath); // write data to a file or to a PHP stream
        $writer->openToBrowser($fileName); // stream data directly to the browser

        $values = [
            __('Order number'),
            __('Date'),
            __('Status'),
            __('First name'),
            __('Last name'),
            __('Phone number'),
            __('Product'),
            __('Quantity'),
            __('Products info'),
            __('Photo'),
            __('Total'),
        ];
        $rowFromValues = WriterEntityFactory::createRowFromArray($values);
        $writer->addRow($rowFromValues);

        foreach ($ordersAll as $order) {
            $values = [
                $order->id,
                Helper::formatDateTime($order->created_at),
                $order->status_text,
                $order->first_name,
                $order->last_name,
                $order->phone_number,
                $order->product->name,
                $order->quantity,
                $order->products_info,
                ($order->image) ? Storage::disk('public')->url($order->image) : '',
                $order->total,
            ];
            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
            $writer->addRow($rowFromValues);
        }

        $writer->close();
        exit();
    }

    public function upload(Request $request)
    {
        return view('orders.upload');
    }

    public function uploadStore(Request $request)
    {
        $request->validate([
            'upload' => 'required|mimes:xlsx',
        ]);
        $request->file('upload')->storeAs('uploads', 'orders-upload.xlsx');
        $uploadedQuantity = 0;

        $filePath = Storage::path('uploads/orders-upload.xlsx');
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($filePath);
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $key => $row) {
                if ($key == 1) {
                    // title row
                    continue;
                }
                $cells = $row->getCells();
                $productID = $cells[3]->getValue();
                $product = Product::where('id', $productID)->first();
                if (!$product) {
                    continue;
                }
                $quantity = (int)$cells[4]->getValue();
                $data = [
                    'product_id' => $product->id,
                    'info' => $product->name,
                    'total' => $product->price * $quantity,
                    'first_name' => $cells[0]->getValue(),
                    'last_name' => $cells[1]->getValue(),
                    'phone_number' => $cells[2]->getValue(),
                    'quantity' => $quantity,
                    'products_info' => $cells[5]->getValue(),
                    'status' => Order::STATUS_OPEN,
                ];
                $order = Order::create($data);
                $uploadedQuantity++;
            }
            break;
        }

        $reader->close();

        return redirect()->route('orders.index')->with('success', __('Orders uploaded') . ': ' . $uploadedQuantity);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $order = new Order();
        $products = $this->products();
        return view('orders.create', compact('order', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        Order::create($data);
        return redirect($this->page)->with('success', 'Order saved');
    }

    /**
     * Display the specified resource.
     *
     * @param int $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return view('orders.show', compact('order'));
    }

    public function close(Request $request, Order $order)
    {
        $request->validate([
            'notification' => 'required',
        ]);
        $order->status = Order::STATUS_CLOSE;
        $order->save();

        // send notification
        Helper::toTelegram($request->input('notification'), null, $order->telegram_user_id);

        return redirect($this->page)->with('success', 'Order closed');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        $products = $this->products();
        return view('orders.edit', compact('order', 'products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $data = $this->validatedData($request);
        $order->update($data);
        return redirect($this->page)->with('success', 'Order saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        // delete old image
        $order->delete();
        return redirect($this->page)->with('success', 'Order deleted');
    }

    private function validatedData(Request $request, $options = [])
    {
        $rules = [
            'product_id' => 'required',
            'quantity' => 'required|integer',
            'products_info' => 'required|max:65536',
            'first_name' => 'required|max:191',
            'last_name' => 'required|max:191',
            'phone_number' => 'required|max:191',
            'info' => '',
            'status' => 'required|in:-1,0,1',
        ];
        $rules = array_merge($rules, $options);
        $data = $request->validate($rules, [
            '*.required' => __('Required field'),
            '*.image' => __('Upload an image'),
        ]);

        $product = Product::findOrFail($data['product_id']);
        $data['info'] = $product->name;
        $data['total'] = $product->price * $data['quantity'];

        return $data;
    }

    private function products()
    {
        return Product::orderBy('name')->get();
    }

    private function generateQuery(Request $request) {
        $filter = $this->getFilter($request);
        $query = Order::latest();

        $query->where('created_at', '>=', $filter['period_from']->format('Y-m-d H:i:s'))
              ->where('created_at', '<=', $filter['period_to']->format('Y-m-d H:i:s'));

        if ($filter['first_name']) {
            $query->where('first_name', 'LIKE', '%' . $filter['first_name'] . '%');
        }
        if ($filter['last_name']) {
            $query->where('last_name', 'LIKE', '%' . $filter['last_name'] . '%');
        }
        if ($filter['phone_number']) {
            $query->where('phone_number', 'LIKE', '%' . $filter['phone_number'] . '%');
        }
        if ($filter['id']) {
            $query->where('id', $filter['id']);
        }
        if ($filter['status'] != '-') {
            $query->where('status', $filter['status']);
        }

        return $query;
    }

    private function getFilter(Request $request) {
        $periodFrom = $request->input('period_from', Carbon::now()->subMonths(3)->startOfDay());
        $periodTo = $request->input('period_to', Carbon::now()->endOfDay());
        if (is_string($periodFrom)) {
            $periodFrom = Carbon::createFromFormat('d.m.Y', $periodFrom)->startOfDay();
        }
        if (is_string($periodTo)) {
            $periodTo = Carbon::createFromFormat('d.m.Y', $periodTo)->endOfDay();
        }
        $filter = [
            'period_from' => $periodFrom,
            'period_to' => $periodTo,
            'first_name' => $request->input('first_name', ''),
            'last_name' => $request->input('last_name', ''),
            'phone_number' => $request->input('phone_number', ''),
            'id' => $request->input('id', ''),
            'status' => $request->input('status', '-'),
        ];
        return $filter;
    }
}
