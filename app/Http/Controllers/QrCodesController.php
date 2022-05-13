<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrCodes;
use Inertia\Inertia;
use Zxing\QrReader;
use Imagick;

class QrCodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $qrCodes = QrCodes::all();
        return Inertia::render('qrCodes', [
            'data'=>[
                'qrCodes' => $qrCodes,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {

        $request->validate([
            'file_pdf' => 'required|mimes:pdf|max:2048',
            'status'=> 'required|in:Submitted,onProcessing,Processed'
        ]);

        //$qrcode = new QrReader($request->file('file'));
        //$content = $qrcode->text();

        $fileName = $request->file('file_pdf')->getClientOriginalName();
        $qrCode = QrCodes::where('name', $fileName)->first();

        if(!empty($qrCode)){
            return back()
                ->withErrors(['There is another file with the same name.']);
        }

        //$image = new \Imagick($request->file('file_pdf').'[0]');

        //CONVERT PDF TO PNG
        $FileHandle = fopen('image.png', 'w+');

        $curl = curl_init();

        $instructions = '{
            "parts": [
                {
                "file": "document"
                }
            ],
            "output": {
                "type": "image",
                "format": "png",
                "dpi": 500
            }
        }';

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.pspdfkit.com/build',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_POSTFIELDS => array(
                'instructions' => $instructions,
                'document' => new \CURLFILE($request->file('file_pdf'))
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer pdf_live_b5pnkoJ7tbMPUVgHz7UsqXGiW3aJxL0WmhUWGMEtLhG'
            ),
            CURLOPT_FILE => $FileHandle,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        fclose($FileHandle);

        //SEND PNG FILE TO QR DECODER
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.qrserver.com/v1/read-qr-code/',
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_POSTFIELDS => array(
                'MAX_FILE_SIZE' => 1048576,
                'file' => new \CURLFILE('image.png')
            ),
        ));

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: multipart/form-data',
            ));

        $response = curl_exec($curl);
        $dataQRCode = json_decode($response)[0]->symbol[0];

        if($dataQRCode->error){
            return back()
                ->withErrors(['QrCode cannot be saved.'.$dataQRCode->error]);
        }

        $content = $dataQRCode->data;
        curl_close($curl);

        //SAVE INFO INTO DATABASE
        try{
            $qrCode = new QrCodes();
            $qrCode->name = $fileName;
            $qrCode->content = $content;
            $qrCode->status = $request->input('status');
            $qrCode->save();
        }catch(\Exception $e){
            return back()
                ->withErrors(['QrCode cannot be saved. '.$e->getMessage()]);
        }


        // SAVE PDF INTO STORAGE/PRIVATE
       // $request->file('file_pdf')->storeAs('private', $fileName);

        //DELETE PNG FILE
       // unlink('image.png');

        return back()
            ->with('success','You have successfully upload qrCorde.')
            ->with('file_pdf', $qrCode);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $qrCode = QrCodes::find($id);
        if($qrCode){
            $qrCode->delete();
            return back()
                ->with('success','You have successfully deleted qrCorde.');
        }
        return back()
                ->withErrors(['Cannot find QrCode']);
    }
}
