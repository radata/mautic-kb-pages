-- ## NL ##;
-- 1: nl                    (root NL)
-- 2: mijn-account          (parent=1)
-- 2: aanmaken-en-toegang   (parent=2)
-- 4: beheren               (parent=2)




-- ## EN ##;
-- : en                    (root EN)
-- : my-account            (parent=3)
-- : create-and-access     (parent=4)
-- : manage                (parent=4)



# 1
insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('nl',null,0,'Kennisbank','group','Summary','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Content</span></span></p>','brain','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Root Header HTML</span></span></p>','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Root Footer HTML</span></span></p>',null,1200);

    ## 2
    insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('mijn-account',1,0,'Mijn account','group','Summary 1','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Content 1</span></span></p>','user-square',null,null,null,null);
        ### 21
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('annmaken-en-toegang',2,0,'Aanmaken en toegang','group',null,null,null,null,null,null,null);
            insert into kb_pages(slug,parent_id,title,`type`) values('waar-moet-ik-aan-voldoen-om-via-hollandworx-te-werken',21,'Waar moet ik aan voldoen om via Hollandworx te werken?','article');
            insert into kb_pages(slug,parent_id,title,`type`) values('hoe-oud-moet-ik-zijn-om-via-hollandworx-te-mogen-werken',21,'Hoe oud moet ik zijn om via Hollandworx te mogen werken?','article');
            insert into kb_pages(slug,parent_id,title,`type`) values('waarom-wordt-mijn-identiteitsbewijs-gecontroleerd',21,'Waarom wordt mijn identiteitsbewijs gecontroleerd?','article');
            insert into kb_pages(slug,parent_id,title,`type`) values('hoe-werkt-het-met-een-buitenlands-legitimatiebewijs',21,'Hoe werkt het met een buitenlands legitimatiebewijs?','article');

        ### 22
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('beheer',2,0,'Beheer','group',null,null,null,null,null,null,null);
            insert into kb_pages(slug,parent_id,title,`type`) values('ik-ben-mijn-wachtwoord-vergeten',22,'Ik ben mijn wachtwoord vergeten','article');
            insert into kb_pages(slug,parent_id,title,`type`) values('hoe-kan-ik-mijn-account-verwijderen-bij-hollandworx',22,'Hoe kan ik mijn account verwijderen bij Hollandworx?','article');
            insert into kb_pages(slug,parent_id,title,`type`) values('wat-kan-ik-doen-bij-problemen-met-de-app',22,'Wat kan ik doen bij problemen met de app?','article');
            insert into kb_pages(slug,parent_id,title,`type`) values('hoe-kan-ik-mijn-persoonlijke-gegevens-wijzigen',22,'Hoe kan ik mijn persoonlijke gegevens wijzigen?','article');
            insert into kb_pages(slug,parent_id,title,`type`) values('kan-ik-mijn-voorkeuren-van-communicatie-wijzigen',22,'Kan ik mijn voorkeuren van communicatie wijzigen?','article');

    ## 3
    insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('contact',1,0,'Contact','group',null,null,'messages',null,null,null,null);
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('hoe-neem-ik-contact-met-jullie-op',3,0,'Hoe neem ik contact met jullie op?','article',null,null,null,null,null,null,null);
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('hoe-wordt-ik-door-jullie-benaderd',3,0,'Hoe wordt ik door jullie benaderd?','article',null,null,null,null,null,null,null);
        -- Ik heb feedback of ik zou wat willen toevoegen aan de FAQ.

    ## 4
    insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('over-ons',1,0,'Over ons','group',null,null,'building',null,null,null,null);

    ## 5
    insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('voor-opdrachtgevers',1,0,'Voor opdrachtgevers','group',null,null,'briefcase',null,null,null,null);
        -- Samenwerken met Flexwerkers
            -- Hoe werkt het annuleringsbeleid?
            -- Wat doe ik bij onenigheid met de opdrachtnemer?

        -- Gebruik van het platform
            -- Hoe werkt de check-out?
            -- Hoe pas ik een shift aan?
            -- Wat zijn de voordelen van een flexpool?
            -- Ik wil mijn flexpool wijzigen
            -- Hoe nodig ik iemand uit voor een flexpool?
            -- Hoe werkt een flexpool?
            -- Wat is een flexpool?
            -- Hoe verwijder ik een dienst?
            -- Wat is een shiftmanager?
            -- Hoe voeg ik diensten toe?
            -- Kan ik meerdere klussen in een keer plaatsen?
            -- Problemen bij het inloggen

        -- Betalingen & Kosten
            -- Hoe werkt de facturatie voor opdrachtgevers
            -- Waarom ontvang ik een factuur zonder btw?
            -- Hoe wijzig ik mijn factuurgegevens?

        -- Over Hollandworx
            -- Hoe neem ik contact op als opdrachtgever?
            -- Wat is Hollandworx nou eigenlijk?
            -- Wat kost het platform voor flexwerkers?
            -- Waarom kiezen voor Hollandworx?
            -- Hoe werkt Hollandworx?


    ## 6
    insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('freelancen',1,0,'Freelancen','group',null,null,'mood-pin',null,null,null,null);
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('belastingen',6,0,'Belastingen','group',null,null,null,null,null,null,null);
            -- Inkomstenbelasting & btw-aangifte als freelancer
            -- Wat zijn de gevolgen als ik geen btw-aangifte doe?

        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('verzekeringen',6,0,'Verzekeringen','group',null,null,null,null,null,null,null);
            -- Ben ik verzekerd als freelancer via Hollandworx?
            -- Hoe werkt de bedrijfsaansprakelijkheidsverzekering?
            -- Hoe werkt de ongevallenverzekering?
            -- Hoe dien ik een verzekeringsclaim in?

        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('btw-en-kvk',6,0,'Btw en KvK','group',null,null,null,null,null,null,null);
            -- Hoe kan ik een btw-id aanvragen?
            -- Hoe kan ik mijn KvK-nummer of btw-id stopzetten?
            -- Mag ik werken met het btw-id van iemand anders?
            -- Kan ik werken terwijl ik wacht op mijn btw-id?

        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('starten-als-freelancer',6,0,'Starten als frrelancer','group',null,null,null,null,null,null,null);
            insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('hoe-word-ik-freelancer',26,0,'Hoe word ik freelancer?','article',null,null,null,null,null,null,null);
            -- Waar moet ik rekening mee houden als freelancer?
            -- Zit ik ergens aan vast als flexwerker?
            -- Verschil tussen werken als freelancer en werken in loondienst
            -- Kan ik naast werken als freelancer ook werken in loondienst?
            -- Wat is het verschil tussen Hollandworx en een uitzendbureau?

    ## 7 (this file )
    insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('hollandworx-app',1,0,'Hollandworx app','group',null,null,'device-mobile-heart',null,null,null,null);
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('aanmelden',7,0,'Aanmelelden','group',null,null,null,null,null,null,null);
            -- Hoe kan ik mij aanmelden voor shifts?
            -- Kan ik mij aanmelden voor alle shifts?
            -- Wat is het verschil tussen een shift claimen en je aanmelden voor een shift?
            -- Hoe weet ik of een aanmelding geaccepteerd is?
            -- Hoeveel tijd heeft een opdrachtgever de tijd om een aanmelding goed te keuren?
            -- Hoeveel tijd heeft een opdrachtgever om een aanmelding te annuleren?
            -- Hoe annuleer ik mijn aanmelding?
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('shifts',7,0,'Shifts','group',null,null,null,null,null,null,null);
            -- Hoe werkt het regelen van vervanging voor een shift?
            -- Wat gebeurt als je niet aanwezig bent bij je geclaimde shift?
            -- Kan ik een shift cancelen?
            -- Kan ik in de app ook shifts filteren?
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('flexpools',7,0,'Flexpools','group',null,null,null,null,null,null,null);
            -- Hoe kom ik in een flexpool terecht?
            -- Hoe kan ik zien of een shift buiten of binnen de flexpool is?

        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('agreements',7,0,'Afspraken','group',null,null,null,null,null,null,null);
            -- Wat moet ik doen als ik niet op mijn dienst kan komen?
            -- Wat kan ik doen aan een onenigheid met mijn opdrachtgever?
            -- Mag een opdrachtgever andere taken van mij vragen?
            -- Wat als ik eerder naar huis moet?

    ## 8 (this file )
    insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('geld-zaken',1,0,'Geld zaken','group',null,null,'currency-euro',null,null,null,null);
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('betaling',13,0,'Betaling','group',null,null,null,null,null,null,null);
            -- Hoe werkt ons uitbetalingsysteem?
            -- Hoe werkt de betaling?
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('administratie',13,0,'Administratie','group',null,null,null,null,null,null,null);
            -- Hoe zit het met de administratie?
            -- Wat is de Kleineondernemingsregeling (KOR)?
            -- Worden mijn reiskosten vergoed?
            -- Ontvang ik vakantiegeld?
            -- Hoe zit het met mijn pensioen?
            -- Heeft freelancen effect op mijn studiefinanciering?
            -- Waarom heb ik geen btw ontvangen over mijn eerste drie diensten?
            -- Wat zijn de voordelen van het openen van een zakelijke rekening?
        insert into kb_pages(slug,parent_id,position,title,`type`,summary,content,icon,header_html,footer_html,custom_css,container_width) values('uren-en-checkout',13,0,'Uren en checkout','group',null,null,null,null,null,null,null);
            -- Ik heb mijn uren verkeerd doorgegeven, wat nu?
            -- Ik wil het tegenvoorstel van mijn uren bespreken.
            -- Hoe werkt de checkout?



# 7
insert into kb_pages(id,is_published,date_added,created_by,created_by_user,date_modified,modified_by,modified_by_user,checked_out,checked_out_by,checked_out_by_user,title,slug,`type`,summary,content,icon,position,parent_id,header_html,footer_html,custom_css,container_width) values(7,1,'2026-03-19 15:01:06',1,null,'2026-03-19 15:01:06',1,null,null,null,null,'Beheren','beheren','group','Summary','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Content</span></span></p>','user-edit',0,2,null,null,null,null);
-- Articles under beheren (parent_id=7) → IDs 17-20


# 5
insert into kb_pages(id,is_published,date_added,created_by,created_by_user,date_modified,modified_by,modified_by_user,checked_out,checked_out_by,checked_out_by_user,title,slug,`type`,summary,content,icon,position,parent_id,header_html,footer_html,custom_css,container_width) values(5,1,'2026-03-19 14:58:23',1,null,'2026-03-19 14:58:23',1,null,null,null,null,'Aanmaken en toegang','aanmaken-en-toegang','group','Summary','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Content</span></span></p>','user-plus',0,2,null,null,null,null);
-- Remaining articles under aanmaken-en-toegang (parent_id=5) → IDs 10-12


# 3
insert into kb_pages(id,is_published,date_added,created_by,created_by_user,date_modified,modified_by,modified_by_user,checked_out,checked_out_by,checked_out_by_user,title,slug,`type`,summary,content,icon,position,parent_id,header_html,footer_html,custom_css,container_width) values(3,1,'2026-03-19 10:52:49',1,null,'2026-03-19 10:52:49',1,null,null,null,null,'Knowledge Base','en','group','Summary','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Content</span></span></p>','brain',0,null,'<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Root Header HTML</span></span></p>','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Root Footer HTML</span></span></p>',null,1200);

insert into kb_pages(slug,parent_id,title,`type`) values('contact',3,'Contact','group');
-- EN top-level categories (parent_id=3) → IDs 39-52
insert into kb_pages(slug,parent_id,title,`type`) values('about-us',3,'Aboux us','group');
insert into kb_pages(slug,parent_id,title,`type`) values('for-clients',3,'For clients','group');
insert into kb_pages(slug,parent_id,title,`type`) values('freelancing',3,'Freelancing','group');
insert into kb_pages(slug,parent_id,title,`type`) values('starting-as-a-freelancer',3,'Starting as a freelancer','group');
insert into kb_pages(slug,parent_id,title,`type`) values('hollandworx-app',3,'Hollandworx app','group');
insert into kb_pages(slug,parent_id,title,`type`) values('agreements',3,'Agreements','group');
insert into kb_pages(slug,parent_id,title,`type`) values('applications',3,'Applications','group');
insert into kb_pages(slug,parent_id,title,`type`) values('flexpools',3,'Flexpools','group');
insert into kb_pages(slug,parent_id,title,`type`) values('shifts',3,'Shifts','group');
insert into kb_pages(slug,parent_id,title,`type`) values('money-matters',3,'Money matters','group');
insert into kb_pages(slug,parent_id,title,`type`) values('payment',3,'Payment','group');
insert into kb_pages(slug,parent_id,title,`type`) values('administration',3,'Administration','group');
insert into kb_pages(slug,parent_id,title,`type`) values('hours-and-checkout',3,'Hours and checkout','group');
# 4
insert into kb_pages(id,is_published,date_added,created_by,created_by_user,date_modified,modified_by,modified_by_user,checked_out,checked_out_by,checked_out_by_user,title,slug,`type`,summary,content,icon,position,parent_id,header_html,footer_html,custom_css,container_width) values(4,1,'2026-03-19 14:27:00',1,null,'2026-03-19 14:27:00',1,null,null,null,null,'My Account','my-account','group','Summary 1','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Content 1</span></span></p>','user-square',0,3,null,null,null,null);


# 6
insert into kb_pages(id,is_published,date_added,created_by,created_by_user,date_modified,modified_by,modified_by_user,checked_out,checked_out_by,checked_out_by_user,title,slug,`type`,summary,content,icon,position,parent_id,header_html,footer_html,custom_css,container_width) values(6,1,'2026-03-19 15:00:24',1,null,'2026-03-19 15:00:24',1,null,null,null,null,'Create and access','create-and-access','group','Summary','<p>Content</p>','user-plus',0,4,null,null,null,null);
-- Articles under create-and-access (parent_id=6) → IDs 13-16
insert into kb_pages(slug,parent_id,title,`type`) values('waar-moet-ik-aan-voldoen-om-via-hollandworx-te-werken',6,'Waar moet ik aan voldoen om via Hollandworx te werken?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-oud-moet-ik-zijn-om-via-hollandworx-te-mogen-werken',6,'Hoe oud moet ik zijn om via Hollandworx te mogen werken?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('waarom-wordt-mijn-identiteitsbewijs-gecontroleerd',6,'Waarom wordt mijn identiteitsbewijs gecontroleerd?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('why-is-my-id-checked',6,'Why is my ID checked?','article');


# 8
insert into kb_pages(id,is_published,date_added,created_by,created_by_user,date_modified,modified_by,modified_by_user,checked_out,checked_out_by,checked_out_by_user,title,slug,`type`,summary,content,icon,position,parent_id,header_html,footer_html,custom_css,container_width) values(8,1,'2026-03-19 15:01:35',1,null,'2026-03-19 15:01:35',1,null,null,null,null,'Manage','manage','group','Summary','<p><span style="background-color:rgb(255,255,255);color:rgb(82,82,82);font-size:14px;"><span style="-webkit-text-stroke-width:0px;display:inline !important;float:none;font-family:&quot;Source Sans 3&quot;, &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif;font-style:normal;font-variant-caps:normal;font-variant-ligatures:normal;font-weight:400;letter-spacing:normal;orphans:2;text-align:start;text-decoration-color:initial;text-decoration-style:initial;text-decoration-thickness:initial;text-indent:0px;text-transform:none;white-space:normal;widows:2;word-spacing:0px;">Content</span></span></p>','user-edit',0,4,null,null,null,null);
-- Articles under manage (parent_id=8) → IDs 21-24
insert into kb_pages(slug,parent_id,title,`type`) values('how-can-i-change-my-personal-information',8,'How can I change my personal information?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-can-i-delete-my-account-at-hollandworx',8,'How can I delete my account at Hollandworx?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('i-forgot-my-password',8,'I forgot my password','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-can-i-do-if-there-are-problems-with-the-app',8,'What can I do if there are problems with the app?','article');





-- NL article under starten-als-freelancer (parent_id=26) → ID 53
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-word-ik-freelancer',26,'Hoe word ik freelancer?','article');

-- NL articles under aanmeldingen (parent_id=29) → IDs 54-60
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-kan-ik-mij-aanmelden-voor-shifts',29,'Hoe kan ik mij aanmelden voor shifts?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('kan-ik-mij-aanmelden-voor-alle-shifts',29,'Kan ik mij aanmelden voor alle shifts?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('wat-is-het-verschil-tussen-een-shift-claimen-en-je-aanmelden-voor-een-shift',29,'Wat is het verschil tussen een shift claimen en je aanmelden voor een shift?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-weet-ik-of-een-aanmelding-geaccepteerd-is',29,'Hoe weet ik of een aanmelding geaccepteerd is?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoeveel-tijd-heeft-een-opdrachtgever-om-een-aanmelding-goed-te-keuren',29,'Hoeveel tijd heeft een opdrachtgever om een aanmelding goed te keuren?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoeveel-tijd-heeft-een-opdrachtgever-om-een-aanmelding-te-annuleren',29,'Hoeveel tijd heeft een opdrachtgever om een aanmelding te annuleren?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-annuleer-ik-mijn-aanmelding',29,'Hoe annuleer ik mijn aanmelding?','article');

-- NL articles under shifts (parent_id=30) → IDs 61-64
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-werkt-het-regelen-van-vervanging-voor-een-shift',30,'Hoe werkt het regelen van vervanging voor een shift?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('wat-gebeurt-als-je-niet-aanwezig-bent-bij-je-geclaimde-shift',30,'Wat gebeurt als je niet aanwezig bent bij je geclaimde shift?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('kan-ik-een-shift-cancelen',30,'Kan ik een shift cancelen?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('kan-ik-in-de-app-ook-shifts-filteren',30,'Kan ik in de app ook shifts filteren?','article');

-- NL articles under flexpools (parent_id=31) → IDs 65-66
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-kom-ik-in-een-flexpool-terecht',31,'Hoe kom ik in een flexpool terecht?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-kan-ik-zien-of-een-shift-buiten-of-binnen-de-flexpool-is',31,'Hoe kan ik zien of een shift buiten of binnen de flexpool is?','article');

-- NL articles under afspraken (parent_id=32) → IDs 67-70
insert into kb_pages(slug,parent_id,title,`type`) values('wat-moet-ik-doen-als-ik-niet-op-mijn-dienst-kan-komen',32,'Wat moet ik doen als ik niet op mijn dienst kan komen?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('wat-kan-ik-doen-aan-een-onenigheid-met-mijn-opdrachtgever',32,'Wat kan ik doen aan een onenigheid met mijn opdrachtgever?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('mag-een-opdrachtgever-andere-taken-van-mij-vragen',32,'Mag een opdrachtgever andere taken van mij vragen?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('wat-als-ik-eerder-naar-huis-moet',32,'Wat als ik eerder naar huis moet?','article');

-- NL articles under betaling (parent_id=34) → IDs 71-73
insert into kb_pages(slug,parent_id,title,`type`) values('wie-of-wat-is-payday',34,'Wie of wat is Payday?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-werkt-de-betaling',34,'Hoe werkt de betaling?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-maak-ik-een-payday-account-aan',34,'Hoe maak ik een Payday account aan?','article');

-- NL articles under administratie (parent_id=35) → IDs 74-81
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-zit-het-met-de-administratie',35,'Hoe zit het met de administratie?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('wat-is-de-kleineondernemingsregeling-kor',35,'Wat is de kleineondernemersregeling (KOR)?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('worden-mijn-reiskosten-vergoed',35,'Worden mijn reiskosten vergoed?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('ontvang-ik-vakantiegeld',35,'Ontvang ik vakantiegeld?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-zit-het-met-mijn-pensioen',35,'Hoe zit het met mijn pensioen?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('heeft-freelancen-effect-op-mijn-studiefinanciering',35,'Heeft freelancen effect op mijn studiefinanciering?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('waarom-heb-ik-geen-btw-ontvangen-over-mijn-eerste-drie-diensten',35,'Waarom heb ik geen btw ontvangen over mijn eerste drie diensten?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('wat-zijn-de-voordelen-van-het-openen-van-een-zakelijke-rekening',35,'Wat zijn de voordelen van het openen van een zakelijke rekening?','article');

-- NL articles under uren-en-checkout (parent_id=36) → IDs 82-84
insert into kb_pages(slug,parent_id,title,`type`) values('ik-heb-mijn-uren-verkeerd-doorgegeven-wat-nu',36,'Ik heb mijn uren verkeerd doorgegeven, wat nu?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('ik-wil-het-tegenvoorstel-van-mijn-uren-bespreken',36,'Ik wil het tegenvoorstel van mijn uren bespreken','article');
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-werkt-de-checkout',36,'Hoe werkt de checkout?','article');

-- NL article under contact (parent_id=38) → ID 85
insert into kb_pages(slug,parent_id,title,`type`) values('hoe-neem-ik-contact-op-met-hollandworx',38,'Hoe neem ik contact op met Hollandworx?','article');

-- EN article under starting-as-a-freelancer (parent_id=42) → ID 86
insert into kb_pages(slug,parent_id,title,`type`) values('how-do-i-become-a-freelancer',42,'How do I become a freelancer?','article');

-- EN articles under agreements (parent_id=44) → IDs 87-90
insert into kb_pages(slug,parent_id,title,`type`) values('can-a-client-request-other-tasks-from-me',44,'Can a client request other tasks from me?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-can-i-do-about-a-disagreement-with-my-client',44,'What can I do about a disagreement with my client?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-if-i-have-to-go-home-early',44,'What if I have to go home early?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-should-i-do-if-i-cannot-make-it-to-my-shift',44,'What should I do if I cannot make it to my shift?','article');

-- EN articles under applications (parent_id=45) → IDs 91-97
insert into kb_pages(slug,parent_id,title,`type`) values('can-i-register-for-all-shifts',45,'Can I register for all shifts?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-do-i-cancel-my-registration',45,'How do I cancel my registration?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-do-i-know-if-an-application-has-been-accepted',45,'How do I know if an application has been accepted?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-do-i-sign-up-for-shifts',45,'How do I sign up for shifts?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-much-time-does-a-client-have-to-approve-a-registration',45,'How much time does a client have to approve a registration?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-much-time-does-a-client-have-to-cancel-a-registration',45,'How much time does a client have to cancel a registration?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-is-the-difference-between-claiming-a-shift-and-signing-up-for-a-shift',45,'What is the difference between claiming a shift and signing up for a shift?','article');

-- EN articles under flexpools (parent_id=46) → IDs 98-99
insert into kb_pages(slug,parent_id,title,`type`) values('how-can-i-see-whether-a-shift-is-outside-or-inside-the-flex-pool',46,'How can I see whether a shift is outside or inside the flex pool?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-do-i-end-up-in-a-flex-pool',46,'How do I end up in a flex pool?','article');

-- EN articles under shifts (parent_id=47) → IDs 100-103
insert into kb_pages(slug,parent_id,title,`type`) values('can-i-also-filter-shifts-in-the-app',47,'Can I also filter shifts in the app?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('can-i-cancel-a-shift',47,'Can I cancel a shift?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-does-arranging-replacement-for-a-shift-work',47,'How does arranging replacement for a shift work?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('wat-gebeurt-als-je-niet-aanwezig-bent-bij-je-geclaimde-shift',47,'What happens if you are not present at your claimed shift?','article');

-- EN articles under payment (parent_id=49) → IDs 104-106
insert into kb_pages(slug,parent_id,title,`type`) values('how-do-i-create-a-payday-account',49,'How do I create a Payday account?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('how-does-the-payment-work',49,'How does the payment work?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('who-or-what-is-payday',49,'Who or what is Payday?','article');

-- EN articles under administration (parent_id=50) → IDs 107-114
insert into kb_pages(slug,parent_id,title,`type`) values('do-i-receive-holiday-pay',50,'Do I receive holiday pay?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('does-freelancing-affect-my-student-finance',50,'Does freelancing affect my student finance?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-about-my-pension',50,'What about my pension?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-about-the-administration',50,'What about the administration?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-are-the-benefits-of-opening-a-business-account',50,'What are the benefits of opening a business account?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('what-is-the-small-business-scheme-kor',50,'What is the small business scheme (KOR)?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('why-didnt-i-receive-vat-on-my-first-three-services',50,'Why didn''t I receive VAT on my first three services?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('will-my-travel-expenses-be-reimbursed',50,'Will my travel expenses be reimbursed?','article');

-- EN articles under hours-and-checkout (parent_id=51) → IDs 115-117
insert into kb_pages(slug,parent_id,title,`type`) values('how-does-the-checkout-work',51,'How does the checkout work?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('i-entered-my-hours-incorrectly-what-now',51,'I entered my hours incorrectly, what now?','article');
insert into kb_pages(slug,parent_id,title,`type`) values('i-would-like-to-discuss-the-counter-proposal-of-my-hours',51,'I would like to discuss the counter proposal of my hours','article');

-- EN article under contact (parent_id=52) → ID 118
insert into kb_pages(slug,parent_id,title,`type`) values('how-do-i-contact-hollandworx',52,'How do I contact Hollandworx?','article');
