<?php

namespace Necktie\GatewayBundle\Controller;

use Necktie\GatewayBundle\Entity\Ipn;
use Necktie\GatewayBundle\Entity\VendorIpn;
use Necktie\GatewayBundle\Entity\VendorUrl;
use OAuth\Common\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaymentController
 * @package Necktie\GatewayBundle\Controller
 *
 * @Route("/payment")
 */
class PaymentController extends Controller
{
    
    /**
     * @Route("/{paySystem}/{type}/{vendor}")
     * @param Request $request
     * @param string $paySystem
     * @param string $type
     * @param string $vendor
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \LogicException
     */
    public function indexAction(Request $request, string $paySystem, string $type, string $vendor)
    {
        $content = [
            'content' => $request->getContent(),
            'vendor'  => $vendor,
            'notificationType' => $type,    //ipn or webhook
            'paySystem' => $paySystem,
//            'method'  => $request->getMethod(), // should be only POST
            'method'  => 'POST',
        ];

        $em = $this->getDoctrine()->getManager();
        $ipn = new Ipn($request->getContent());
        $em->persist($ipn);
        $em->flush();

        $ipnContent = '{"notification":"GTTaza/R9WLxNe0ija/CK3H7yxL6nR1ozXLkJz6jnzuQJYuPy631Dh61ssQ6Ujlztccu0R4LRKvaIzgnxQRJenkQu08zhIculln4rM8K4TZuqWNdx9I/5eK3pZmhAEk+XP5H+s1144v3isKbwOHolQQTjxY7T63FDkrGMikbKWkIXwr98zVFb/FzOOvf1sC2atDf4uoGjJQRej47cK0Cbz84i7hG7gFGCUqQ4KKLaujyv88OqksN/Mt1+i70++L9lcu8Mp9D73i7Y3dcXNZvxD1xHQoJiq9WwKFAOlGHp0hqH+s9kV7Dy4N687ZqCn3uXzFWfRzrUeex5N8m9bdAfR/w8zJWul2ntJBOCtwS9YGJO4gWzzx1bWXzAX0WJ1NKgoRBX3U6dj9nCvD1HfCakHvr5SGLp+fimsGhAcfIynbb9Y0LSkoG8/7glV0uCZnvEgOIf/TRDNovzLEORrnBHrFUjr1kZH01P6F6HI/RQY0Ir10/zqu93eEUiG0aUi0k7yo+KunqsXxOsLTTwbVSYF/AQXH87i8bjsw3HHE82VBqe6EXAZz9usbXUy1zzJ+0MPP8m7niZvVayYLmyIML4eZgHAbUZevBK4YzVTDlhwp0B5JR7ZCHAdZclevMye0OdeiPyYNZt1FaKnGqMzFkS2vVFWFfiykcJu0NW0jZx2caRICo4xVyvpdUY/IdN8+TbYarLuXycN8nTtxRYxH/Y2lYt9TpChn1R88WcuO2qy2EJHNwxBwhOzsEH3xJu8Px0pDrqQi5lMYR+VE7RwzNRSXQDVO2tphS/dx8wxpl4VKudDCUrICBUQxFU5vgQknGoIWQ/FRgdjCiKwaHxEMX5g8FyhjMmGhZjRq3aVjrhsAoj61SO9Hz6P9cTqhQKEjThoixQRuvk31nvSzUMuXylAWdCsxvWZviIii6BFokOvlkZH/ayVpMIwE/+Jn1769+EemdBA+FT2dIwNd3mm+HsB3sBV1Lwz/x/U3I/UUkRa5aZjAuewiYZFAI07XfLoz+iOZYE+9hIxXA0ykRyIUHcoP5MXvkx7WqojQ5bsb7ODXS9L+RYeQ09pNNpqLGe/FrO+lw5Mu8HIZY/PDQ3pCqlVzwRtAzzJXMUa2AAAzA48Zo+J/8CGidC5L/9z1LZzLmePn94wBoUw7ixw4RQHPdvozd8C0rBJZ6PT9EJ6cRabDb2zVTq0uSfp35ycMf4K00jV46hwCfqJddistIbamXUH1yqePYUNWAFHZXFFBra3+scCgoMs0bNUk1GoPq2+eHnrRp/yYGrHZFVgTK9k7v/y10hPGrtBUmW31b8OpQjPAOF4xOFz9sftOPLPtZ32/WvycQ+FbskuTDvCQtMHpUEd21/+QOVqZa2cZtihGbufz05NAgmo/HqlGnCSH7vTZVjY4MSD56N6IIxRMoWwlNpZ6H93LEpoIhp+Ai0BOq6bcu8llk0Ki1LmPe5wlCb4Z1Uo2pl0Qjr2/i2absusC0RwD87Agj/cQUets8f1GMSWSFy998tpwPw2uHJXkTo/RXD5CeVTGfCS6nOnC5/CoDp7RvKvTrVk+BMCzp5A8n5/g69lTlHlt+UJFyQcvywD4an3E7OuyJjmnK/WyM50UI6WdGLrOLLLeYwVjc85V9PWzBhkFTmN9GM/ShFpa4WVBYuj/uJbQLQNmCNii6TrObxMj1Ik+PqQpdYvXaw8uPr0HNw0nXjYcrnJi+ZXDMmSuiQjSR+OIe6/JWcE+ME4sgLsf66i/lzqTAq/UxGCuGrMMEn7+LaB/dnbni2fboUUq+XsNpYT1lgVOuIKgy8+mqDS1o7SZ5bVwiIwsJoTeVwGr9rjuTvHkAzCczcB6/dGZ+erSY0y1Yv7P1MBT8F6qFhb3M/QhIhOre4upJW9kw2nsA4Y1ZxDIk+iCJ2Y7qQcvGXN0WtkbKQI0uZ7M5QNZhh2qgPq/J28AGIRu1dBw8ZgTRkAWT2/PCshmxEo35+sFKEC8ujwL6oI1+zeRefdEr31+OLqL+MeZpKHTHGoRyHekBb9iuI9dnPJCFPqSJPWa87uWp0zp+3mGFvrn9s7fDWPsTZUaTCBQ271mjDAQWd10yKW+fcHVHOdzKPZRCZrtMpRu8zdGPOsvvtOclrOoNIVqnB8LMyLDnTO3HdcDgEtKt3FvLKh5c9CbdQ/1TqkjAfva/BivQv3gqIKJD50X8QLfGOKJU/L4OJw8sAm39LQcQBHasTMKV96+ZZtL/Bs8/X8jnGto5y4/a0nO1u/tgSBma63w/umdE12y4uc8ZdQ/lPh2wUajlcIBaFVuI6Wv4PVaHHTVc/GFMt4IOfhKxkSkVQ+bWg+cX0zxCBnYZIrVcu+ZXOg60EP5QRB1rV0kE1lLu1GJogU48oHNclzRuve27BgAd4xtKW91bfJekieM9p6gCEdjJB1275HGd77b6Q1qFWPHww7XpemjeYYuvqfJ1Vdn4X3ylfoel7rcGpcj/aGxO4omD2quvTxvf8Gu5","iv":"QzIzNUIwMDlGN0UwRTBGNw=="}';

        $this->getDoctrine()->getManager();
        $vendorUrls = $em->getRepository('GatewayBundle:VendorUrl')->findBy(
            ['vendor' => $vendor],
            ['priority' => 'DESC']
        );

        $this->get('gw.message.processor')->addProcessor($this->get('gw.processor.httpcheck'));
        /** @var VendorUrl $vendorUrl */
        foreach ($vendorUrls as $vendorUrl) {
            $vendorIpn = new VendorIpn($vendorUrl, $ipn);
            $em->persist($vendorIpn);
            $em->flush();

            $message = [
                'method' => 'POST',
//            'method'  => $request->getMethod(), // should be only POST,
                'url' => $vendorUrl->getUrl(),
                'header' => [],
                'body' => $ipnContent,
                'tag' => '',
                'attributes' => ['vendorIpnId' => $vendorIpn->getId()],
                'processorName' => 'HTTPCheckProcessor'
            ];
            $this->get('payment_redirect.producer')->publish(
                serialize($message)
            );

            $message['processorName'] = 'DecryptProcessor';

            $this->get('decrypt.producer')->publish(
                serialize($message)
            );

        }

        return $this->json(['message' => 'OK '], 200);
//        return $this->json($message, 200);
    }
}
