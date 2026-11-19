<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;

use Auth;
use Illuminate\Notifications\Messages\BroadcastMessage;

//use App\Events\SubscriptionRequest;
use Illuminate\Support\Str;

class SendReceipt extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public $user, $pdf, $receipt, $module;

    public function __construct($user, $pdf, $receipt, $moduleReq)
    {
       $this->user = $user;
       $this->pdf = $pdf;
       $this->receipt = $receipt;
       $this->module = $moduleReq;
       //dd($this->user);

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
      $receipt = json_decode($this->receipt);
      $receiptDetails = json_decode($receipt[0]->details);
      //dd($receiptDetails);

      $html = '<table width="100%" style="border:1px solid #666; padding: 10px;">';
      if(isset($receiptDetails->membershipType) && $receiptDetails->membershipType != ''){
        $html .= '<tr class="items">';
        $html .= '  <td width="200">Tipe Keanggotaan</td>';
        $html .= '  <td>'. $receiptDetails->membershipType .'</td>';
        $html .= '</tr>';
      }

      foreach($receiptDetails as $key => $val){

          if ($key == 'spacer1' || $key == 'spacer2'){
            $html .= '<tr class="items">';
            $html .= '  <td colspan="2"><hr></td>';
            $html .= '</tr>';
          }else{
              if ($key == 'membershipType'){

              }else{
                if ($key == 'item_list'){
                  foreach($val as $product){
                    $html .= '<tr class="items">';
                    $html .= '<td width="200">'. $product->item .'</td>';
                    $html .= '<td align="right">'. $product->qty .' x '. $product->price .'</td>';
                    $html .= '</tr>';
                  }
                    $html .= '<tr class="items">';
                    $html .= '<td colspan="2"><hr></td>';
                    $html .= '</tr>';
                }else{


                  $html .= '<tr class="items">';
                  $html .= '  <td width="100">';
                  if ($key == 'payment_method_not_1' || $key == 'payment_method_1'){

                  }else{
                    $html .=  $key .':';
                  }

                  $html .= '  </td>';

                  $html .= '  <td>'.  $val .'</td>';

                  $html .= '</tr>';
                }
              }
          }
      }

      $html .= '<tr class="items">';
      $html .= ' <td colspan="2"><hr></td>';
      $html .= '</tr>';

      $html .= '<tr class="total">';
      $html .= '  <td colspan="2" align="right"><b>Total: '. $receiptDetails->Total .'</b></td>';
      $html .= '</tr>';
      $html .= '</table>';

      //dd($html);
      $htmlBody = '';
      switch ($this->module) {
        case 'membership':
          $htmlBody = '<p><br>Terima kasih telah bergabung bersama Curves!<br>Kami dengan senang hati mengonfirmasi bahwa pembelian membership Anda telah berhasil diproses.</p>';
          $htmlBody .= '<p>Coach Curves akan segera menghubungi Anda untuk sesi latihan pertama.<br>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.</p>';
          $htmlBody .= '<p>Selamat memulai perjalanan sehat Anda bersama Curves! 💪</p>';

          break;

        case 'renewal':
          $htmlBody = '<p><br>Terima kasih telah bergabung bersama Curves!<br>Kami dengan senang hati mengonfirmasi bahwa pembelian perpanjangan membership Anda telah berhasil diproses.</p>';
          $htmlBody .= '<p>Coach Curves akan segera menghubungi Anda untuk sesi Latihan.</p>';
          $htmlBody .= '<p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.</p>';
          $htmlBody .= '<p>Selamat memulai perjalanan sehat Anda bersama Curves! 💪</p>';
          break;

        case 'locker':
          $htmlBody = '<p><br>Terima kasih telah bergabung bersama Curves!<br>Kami dengan senang hati mengonfirmasi bahwa pembelian Loker Anda telah berhasil diproses.</p>';
          $htmlBody .= '<p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.</p>';
          $htmlBody .= '<p>Selamat memulai perjalanan sehat Anda bersama Curves! 💪</p>';
          break;

        case 'product':
          $htmlBody = '<p><br>Terima kasih telah bergabung bersama Curves!<br>Kami dengan senang hati mengonfirmasi bahwa pembelian produk Anda telah berhasil diproses.</p>';
          $htmlBody .= '<p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.</p>';
          $htmlBody .= '<p>Selamat memulai perjalanan sehat Anda bersama Curves! 💪</p>';
          break;

        case 'promo':
          $htmlBody = '<p><br>Terima kasih telah bergabung bersama Curves!<br>Kami dengan senang hati mengonfirmasi bahwa pembelian promo memberships Anda telah berhasil diproses.</p>';
          $htmlBody .= '<p>Coach Curves akan segera menghubungi Anda untuk sesi latihan pertama.</p>';
          $htmlBody .= '<p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.</p>';
          $htmlBody .= '<p>Selamat memulai perjalanan sehat Anda bersama Curves! 💪</p>';
          break;

        case 'promoconvert':
          $htmlBody = '<p><br>Terima kasih telah bergabung bersama Curves!<br>Kami dengan senang hati mengonfirmasi bahwa pembelian membership Anda telah berhasil diproses.</p>';
          $htmlBody .= '<p>Coach Curves akan segera menghubungi Anda untuk sesi latihan pertama.</p>';
          $htmlBody .= '<p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami.</p>';
          $htmlBody .= '<p>Selamat memulai perjalanan sehat Anda bersama Curves! 💪</p>';
          break;

        default:
          $htmlBody = '<p><br>Selamat memulai perjalanan sehat Anda bersama Curves! 💪</p>';
          break;
      }

        return (new MailMessage)
            ->subject('Payment Receipt')
            ->greeting('Hi '. $this->user->name .',')
            //->line(new HtmlString("<br>Terima kasih telah bergabung bersama Curves!<br>Kami dengan senang hati mengonfirmasi bahwa pembelian membership Anda telah berhasil diproses."))

            //->line(new HtmlString("Coach Curves akan segera menghubungi Anda untuk sesi latihan pertama.<br>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi kami."))
            //->line(new HtmlString("Selamat memulai perjalanan sehat Anda bersama Curves! 💪"))
            ->line(new HtmlString($htmlBody))

            ->line(new HtmlString($html))
            ->line(new HtmlString('<br><br>'))

            ->salutation(new HtmlString('Salam sehat,<br>Curves Indonesia'))

            ->attach($this->pdf, [
              'mime' => 'application/pdf',
            ])
           ;
    }
}
