# Get started with documentation, by Vendor

# Table of contents  <a id="tableOfContentsPhpStorm"></a>

<!-- TOC -->

* [Coopeuch project](#coopeuch-project)
* [Table of contents  <a id="tableOfContentsPhpStorm"></a>](#table-of-contents-a-idtableofcontentsphpstorma)
* [What is the objective of this file? <a id="objective"></a>](#what-is-the-objective-of-this-file-a-idobjectivea)
* [Documentation for each module in app/code](#documentation-for-each-module-in-appcode)
* [Areas, Epics & User Stories relation with each module](#areas-epics--user-stories-relation-with-each-module)

<!-- TOC -->

---

---

---

# What is the objective of this file? <a id="objective"></a>

[Return to the table of contents](#table-of-contents-a-idtableofcontentsphpstorma)

This file will demonstrate the correct location for each **area** of business logic

Each **area** of business logic is a group of **epics**

Each **epic** is a group of **user stories**

Each **user story** is a group of **features** that gives value to customer ot to the project or to the merchant

Let's show with an example:

Each **area** of business logic can be like the module names in the Magento project. The areas of business logic could
be:

- Catalog
- Sales
- Customer
- Etc.

Each **epic** is a big new feature, for example:

- Step Reward Points
- Mercado Pago Payment
- Magento <> Middleware communication

**User stories** are worked in an epic, an example of user story could be:

- (Step Reward Points) Earn Step Reward Points - User will earn Step Reward Points when it buys products in store
- (Step Reward Points) Use Step Reward Points - Allow customers to use Step Reward Points to get products for free

A **feature** it's the most detailed expression of a business rule

- (Step Reward Points) Earn Step Reward Points - User will earn Step Reward Points when it buys products in store
    - Step Reward Points can be earned when user ONLY buys in mobile
    - Step Reward Points can be earned when a user buys equals or MORE than 10,000
      pesos [This amount need to be configured in Admin]
    - Step Reward Points can be earned when a user DOES NOT USE Step Reward Points to buy
    - Step Reward Points can be earned when the order is the FIRST purchase of the day that MEET the previous rules

---

# Documentation for each module in app/code

[Return to the table of contents](#table-of-contents-a-idtableofcontentsphpstorma)

The documentation and the diagrams with the connection to other systems are saved on each module, as described in:

https://brjupo.wordpress.com/2024/01/02/magento-2-module-documentation-best-practices/

---

# Areas, Epics & User Stories relation with each module

[Return to the table of contents](#table-of-contents-a-idtableofcontentsphpstorma)

**Please see the modules "DOCUMENTATION.md" files to identify the relation between each feature and each
file in code**

| Area              | Epic                         | User Story                            | Module                 |
|-------------------|------------------------------|---------------------------------------|------------------------|
| Reward            | Step Reward Points           | Earn Step Reward Points               | Vendor_Reward          |
| Reward            | Step Reward Points           | Earn Step Reward Points               | Vendor_RewardGraphQl   |
| Reward            | Step Reward Points           | Earn Step Reward Points               | Vendor_RewardPoints    |   
| ERP Communication | Magento <> Middleware <> ERP | Communication with Middleware and ERP | Vendor_SyncOrders      |
| Payment           | MercadoPago                  | Add MercadoPago Payment Method        | MercadoPago_Core       |  
| Payment           | MercadoPago                  | Add MercadoPago Payment Method        | Vendor_GenerateInvoice |  

---

# Get Started - With this new way to document coding projects

This information is available at https://brjupo.wordpress.com/2024/01/02/magento-2-module-documentation-best-practices/

This file will serve as a guide, so that anyone can read and edit the module
documentation, in a more orderly and efficient way.

1. The documentation includes:
    1. app/code/Vendor/Module/README.md file [this file]
    2. app/code/Vendor/Module/DOCUMENTATION.md file
    2. app/code/Vendor/Module/DOCUMENTATION.md file
    3. 'Documentation' folder, which includes images, files and code of the
       steps to follow and/or diagrams
2. Some steps can be skipped to speed up the documentation process and because
   you can change between one version of Magento and another, for precise detail,
   the official Magento documentation for the management and use of the 'Admin'
   section is available at:
    1. https://experienceleague.adobe.com/docs/commerce-admin/user-guides/home.html?lang=en
3. To create the diagrams we use interpreted code in a web tool. These tools
   allow us quick and easy editing WITHOUT the need for licenses. The codes will
   be saved with the SAME name as the diagram image, it is a *.txt extension
   *inside the Documentation folder*
    1. There are many tools that you can find by searching on Google 'create
       uml diagram online with code'.
    2. The tool will be used:
        1. https://sequencediagram.org/
        2. https://www.planttext.com/ Select a sample... = Sequence > Comments

