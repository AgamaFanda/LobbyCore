<?php

declare(strict_types=1);

namespace AgamaFanda\LobbyCore;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\server;
use pocketmine\player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

use pocketmine\block\BlockFactory;
use pocketmine\block\Block;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\SchedulerTask;

class Main extends PluginBase implements Listener {

    public function onEnable(){
        $this->getLogger()->info("Plugin Enabled");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getResource("config.yml");
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool{
        if($command->getName() == "lobbycore"){
            $sender->sendMessage("§0-----------------------");
            $sender->sendMessage("§eLobbyCore by AgamaFanda");
            $sender->sendMessage("§aFunctions:");
            $sender->sendMessage("§a- Always spawn");
            $sender->sendMessage("§a- Custom join/quit messages");
            $sender->sendMessage("§a- Navigator/minigame selector");
            $sender->sendMessage("§a- /lobby command with animation");
            $sender->sendMessage("§a- private message when player joins");
            $sender->sendMessage("§a");
            $sender->sendMessage("§eDownload: https://github.com/AgamaFanda/LobbyCore");
            $sender->sendMessage("§0-----------------------");
            return true;
        }
        if($command->getName("lobby")){
            $sender->setImmobile(true);
            $sender->addTitle("§f");
            $sender->addSubTitle("§aTeleporting...");
            $sender->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));

            $this->getScheduler()->scheduleDelayedTask(new class($this, $sender) extends Task{
                protected $main;
                public $player;
            
                    public function __construct(Main $main, CommandSender $player){
                        $this->main = $main;
                        $this->player = $player;
                                
                    }
                        
                public function onRun(int $currentTick){
                    $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20, 2, false));
                    $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));
                }
            },
            10
        );
        $this->getScheduler()->scheduleDelayedTask(new class($this, $sender) extends Task{
            protected $main;
            public $player;
        
                public function __construct(Main $main, CommandSender $player){
                    $this->main = $main;
                    $this->player = $player;
                            
                }
                    
            public function onRun(int $currentTick){
                $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 20, 3, false));
                $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));
            }
        },
        30
    );
    $this->getScheduler()->scheduleDelayedTask(new class($this, $sender) extends Task{
        protected $main;
        public $player;
    
            public function __construct(Main $main, CommandSender $player){
                $this->main = $main;
                $this->player = $player;
                        
            }
                
        public function onRun(int $currentTick){
            $this->player->setImmobile(false);
            $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));
            $this->player->teleport($this->main->getServer()->getLevelByName($this->main->getConfig()->get("lobby-world"))->getSafeSpawn());
            $this->player->sendMessage($this->main->getConfig()->get("lobby-message"));
        }
    },
    30
);
        return true;
        }
        if($command->getName("hub")){
            $sender->setImmobile(true);
            $sender->addTitle("§f");
            $sender->addSubTitle("§aTeleporting...");
            $sender->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));

            $this->getScheduler()->scheduleDelayedTask(new class($this, $sender) extends Task{
                protected $main;
                public $player;
            
                    public function __construct(Main $main, CommandSender $player){
                        $this->main = $main;
                        $this->player = $player;
                                
                    }
                        
                public function onRun(int $currentTick){
                    $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20, 2, false));
                    $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));
                }
            },
            10
        );
        $this->getScheduler()->scheduleDelayedTask(new class($this, $sender) extends Task{
            protected $main;
            public $player;
        
                public function __construct(Main $main, CommandSender $player){
                    $this->main = $main;
                    $this->player = $player;
                            
                }
                    
            public function onRun(int $currentTick){
                $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 20, 3, false));
                $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));
            }
        },
        30
    );
    $this->getScheduler()->scheduleDelayedTask(new class($this, $sender) extends Task{
        protected $main;
        public $player;
    
            public function __construct(Main $main, CommandSender $player){
                $this->main = $main;
                $this->player = $player;
                        
            }
                
        public function onRun(int $currentTick){
            $this->player->setImmobile(false);
            $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));
            $this->player->teleport($this->main->getServer()->getLevelByName($this->main->getConfig()->get("lobby-world"))->getSafeSpawn());
            $this->player->sendMessage($this->main->getConfig()->get("lobby-message"));
        }
    },
    30
);
        return true;
        }
    }
    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        $jpm = $this->getConfig()->get("join-private-message");
        $online = count($this->getServer()->getOnlinePlayers());
        $maxonline = $this->getServer()->getMaxPlayers();
        $jm = $this->getConfig()->get("join-message");
    $joinmsg = str_replace(["[Player]", "[Online_Players]", "[Max_Online]"],
        ["$name", "$online", "$maxonline"],
        $jm);
    $event->setJoinMessage($joinmsg);
    if($this->getConfig()->get("private-message") === true){
        $joinprimsg = str_replace(["[Player]"],
						["$name"],
						$jpm);
					$player->sendMessage($joinprimsg);
    }
            if($this->getConfig()->get("always-spawn") === true){
                $this->getScheduler()->scheduleDelayedTask(new class($this, $player) extends Task{
                    protected $main;
                    public $player;
                
                        public function __construct(Main $main, CommandSender $player){
                            $this->main = $main;
                            $this->player = $player;
                                    
                        }
                            
                    public function onRun(int $currentTick){
                        $this->player->teleport($this->main->getServer()->getLevelByName($this->main->getConfig()->get("lobby-world"))->getSafeSpawn());
                    }
                },
                60
            );
            $this->getScheduler()->scheduleDelayedTask(new class($this, $player) extends Task{
                protected $main;
                public $player;
            
                    public function __construct(Main $main, CommandSender $player){
                        $this->main = $main;
                        $this->player = $player;
                                
                    }
                        
                public function onRun(int $currentTick){
                    if($this->main->getConfig()->get("enable-navigator") === true){
                    $navid = $this->main->getConfig()->get("navigator-id");
                    $navigator = ItemFactory::get($navid, 0, 1);
                    $navigator->setCustomName($this->main->getConfig()->get("navigator-name"));
                    $slot = $this->main->getConfig()->get("navigator-slot");
                    $this->player->getInventory()->removeItem($navigator);
                    $this->player->getInventory()->setItem($slot, $navigator);
                    }
                }
            },
            80
        );
                return true;
            }
    }
    public function onQuit(PlayerQuitEvent $event){
        $qm = $this->getConfig()->get("quit-message");
        $player = $event->getPlayer();
        $name = $player->getName();
        $online = count($this->getServer()->getOnlinePlayers());
        $maxonline = $this->getServer()->getMaxPlayers();
        $leftmsg = str_replace(["[Player]", "[Online_Players]", "[Max_Online]"],
                        ["$name", "$online", "$maxonline"],
                        $qm);
                    $event->setQuitMessage($leftmsg);
    }
    public function onMove(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        $world = $this->getConfig()->get("lobby-world");
        if($this->getConfig()->get("enable-hunger") === false){
            if($player->getLevel()->getName() === $world){
                $player->setFood(20);
                return true;
            }
        }
    }
    public function onClick(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        $navid = $this->getConfig()->get("navigator-id");
    
    if($item->getId() == $navid && $item->getCustomName() === $this->getConfig()->get("navigator-name")){
        $this->openNavigatorForm($player);
        return true;
        }
    }
    public function openNavigatorForm($player){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result === null){
                return true;
            }
            switch($result){
                case 0:
                    $name = $player->getName();
                    $mcc1 = $this->getConfig()->get("command-minigame-1");
                    $mc1 = str_replace(["[Player]"],
                        ["$name"],
                        $mcc1);
                    if($this->getConfig()->get("send.command-as") === "console"){
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $mc1);
                        return true;
                    }else if($this->getConfig()->get("send.command-as") === "player"){
                        $this->getServer()->dispatchCommand($player, $mc1);
                        return true;
                    }
                break;
                case 1:
                    $name = $player->getName();
                    $mcc2 = $this->getConfig()->get("command-minigame-2");
                    $mc2 = str_replace(["[Player]"],
                        ["$name"],
                        $mcc2);
                    if($this->getConfig()->get("send.command-as") === "console"){
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $mc2);
                        return true;
                    }else if($this->getConfig()->get("send.command-as") === "player"){
                        $this->getServer()->dispatchCommand($player, $mc2);
                        return true;
                    }
                break;
                case 2:
                    $name = $player->getName();
                    $mcc3 = $this->getConfig()->get("command-minigame-3");
                    $mc3 = str_replace(["[Player]"],
                        ["$name"],
                        $mcc3);
                    if($this->getConfig()->get("send.command-as") === "console"){
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $mc3);
                        return true;
                    }else if($this->getConfig()->get("send.command-as") === "player"){
                        $this->getServer()->dispatchCommand($player, $mc3);
                        return true;
                    }
                break;
                case 3:
                    $name = $player->getName();
                    $mcc4 = $this->getConfig()->get("command-minigame-4");
                    $mc4 = str_replace(["[Player]"],
                        ["$name"],
                        $mcc4);
                    if($this->getConfig()->get("send.command-as") === "console"){
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $mc4);
                        return true;
                    }else if($this->getConfig()->get("send.command-as") === "player"){
                        $this->getServer()->dispatchCommand($player, $mc4);
                        return true;
                    }
                break;
                case 4:
                    $name = $player->getName();
                    $mcc5 = $this->getConfig()->get("command-minigame-3");
                    $mc5 = str_replace(["[Player]"],
                        ["$name"],
                        $mcc5);
                    if($this->getConfig()->get("send.command-as") === "console"){
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $mc5);
                        return true;
                    }else if($this->getConfig()->get("send.command-as") === "player"){
                        $this->getServer()->dispatchCommand($player, $mc5);
                        return true;
                    }
                break;
                case 5:
                    $name = $player->getName();
                    $mcc5 = $this->getConfig()->get("command-minigame-3");
                    $mc5 = str_replace(["[Player]"],
                        ["$name"],
                        $mcc5);
                    if($this->getConfig()->get("send.command-as") === "console"){
                        $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $mc5);
                        return true;
                    }else if($this->getConfig()->get("send.command-as") === "player"){
                        $this->getServer()->dispatchCommand($player, $mc5);
                        return true;
                    }
                break;
            }
        });
        $form->setTitle("§l§cMinigames");
        $form->setContent(" ");
        $form->addButton($this->getConfig()->get("minigame-1"));
        $form->addButton($this->getConfig()->get("minigame-2"));
        $form->addButton($this->getConfig()->get("minigame-3"));
        $form->addButton($this->getConfig()->get("minigame-4"));
        $form->addButton($this->getConfig()->get("minigame-5"));
        $form->addButton($this->getConfig()->get("minigame-6"));
        $form->sendToPlayer($player);
        return $form;
    }
}
