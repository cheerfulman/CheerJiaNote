<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPost()
    {
        $response = $this->withHeader(
            'x-futu-client-nnid', '123216'
        )->json('POST','/article/post',[
            'title' => '真离谱',
            'content' => '朋友们好啊我是混元桌游社掌门人羊啊羊。刚才有个朋友问我羊老师发生什么事了，我说怎么回事，给我发了一几张截图，我一看！嗷！源濑氏佐田，有两个年轻人，二十多岁，一个玩了九十多把，一个玩了八十多把，他们说，唉…有一个说是我在网杀练悍跳练坏了，羊老师你能不能教教我浑元功法，哎…帮助提高一下，我的悍跳水平。我说可以。我说你在网杀练悍跳，不好用，他不服气。我说小朋友：你拿白狼王牌来悍跳试试，他跳不来。他说你这也没用。我说我这个有用，这是忽悠劲儿，悍跳功夫是忽悠劲儿的，四两拨千金。二百多把的嘤国老玩家，骗不到我一张警徽票。啊…哈！他非和我试试，我说可以。诶…我一说的啪就上警了，很快啊！然后上来就是一个警下诈身份一个天命预言家一个防爆警徽流，我全部掰发言了啊掰完以后自然是传统功夫以点到为止，爆狼发言放到他脸上上没掰他，我笑一下准备下警，由这时间，欸传统功夫的点到为止他已经输了，如果这爆狼发言，一下就把他警徽掰没了了，放在喉咙上没有怼他，他也承认，我先怼到他面部。他不知道爆狼发言怼到他面部，他承认我先怼到他面部，啊，我收声的时间不掰了，他突然给我发了一个查杀来打我脸，啊，我大意了啊，没有回，矮…他查杀我，啊，查杀了一下，但没关系啊！他也说，啊他截图也说了，两分多钟以后，当时我被票出去了，捂着底牌，我说停停。然后两分钟钟以后，两分多钟以后诶好人就输了，我说小伙子你不讲武德你不懂，我说

羊老师对不对不起，我不懂规矩。啊，我是…他说他是乱查杀的，他可不是乱查杀的啊，诈身份防爆警徽流，训练有素，后来他说他练过三四年的谁是卧底，啊，看来是油备而来！这两个年轻人不讲武德，来骗，来偷袭，我十八岁的老同志，这好吗？这不好，我劝这位年轻人耗子尾汁，好好反思，以后不要再犯这样的聪明，小聪明，啊，呃…桌协要以和为贵，要讲武德，不要搞窝里斗，谢谢朋友们！'
        ]);

//        echo $response;
//        $this->assertTrue(true);
        $response->assertStatus(200)->assertJson([

        ]);
    }

    public function testLike(){
        $response = $this->withHeader(
            'x-futu-client-nnid', '398456'
        )->json('POST','/article/like',[
            'article_id' => 3,
        ]);

        $response->assertStatus(200)->assertJson([
            [''=>'']
        ]);
    }
    public function testUnLike(){
        $response = $this->withHeader(
            'x-futu-client-nnid', '993456'
        )->json('POST','/article/unlike',[
            'article_id' => 3,
        ]);

        $response->assertStatus(200)->assertJson([
            [''=>'']
        ]);
    }

    public function testListArticle(){
        $response = $this->withHeader(
            'x-futu-client-nnid', '123456'
        )->json('get','/article/list',[
            'size' => 4,
            'page' => 1,
            'status' => 0
        ]);

        $response->assertStatus(200)->assertJson([
            [
                'size' => 4,
            ]
        ]);
    }

    public function testCensorArticle(){
        $response = $this->withHeader(
            'x-futu-client-nnid', '123456'
        )->withHeader('x-futu-client-staffid' , '123456')->json('post','/article/censor',[
            'article_id' => '2',
            'status' => '2'
        ]);

        $response->assertStatus(200)->assertJson([
            [
                'status' => 2,
            ]
        ]);
    }
}
