<?php if ($currentStep == 3): ?>
                    <h3>Step 3 – Industry/Market Analysis</h3>
                    <p class="company-name-placeholder"><?= $form['company_name'] ?? '-' ?></p>
                        <table class="business-form-table">
                            <thead>
                                <tr>
                                    <th>Variable</th>
                                    <th>1 - Highly Unsatisfactory</th>
                                    <th>2 - Unsatisfactory</th>
                                    <th>3 - Neutral</th>
                                    <th>4 - Satisfactory</th>
                                    <th>5 - Highly Satisfactory</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Porters 1: Threat of Entry
                                        <p class="variable-details">How easy it is for new competitors to enter the business’s market.</p>
                                    </td>
                                    <td><input type="radio" name="threat_of_entry" value="1" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='1') ? 'checked' : '' ?>>The market is very easy to enter; high competitive pressure.</td>
                                    <td><input type="radio" name="threat_of_entry" value="2" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='2') ? 'checked' : '' ?>>Entry moderately easy; noticeable competitive threat.</td>
                                    <td><input type="radio" name="threat_of_entry" value="3" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='3') ? 'checked' : '' ?>>Entry somewhat difficult; moderate competitive pressure.</td>
                                    <td><input type="radio" name="threat_of_entry" value="4" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='4') ? 'checked' : '' ?>>Entry challenging; low competitive threat.</td>
                                    <td><input type="radio" name="threat_of_entry" value="5" <?= (isset($_SESSION['business_form']['threat_of_entry']) && $_SESSION['business_form']['threat_of_entry']=='5') ? 'checked' : '' ?>>The market is very difficult to enter; minimal competitive pressure.</td>
                                </tr>
                                <tr>
                                    <td>Porters 2: Intensity of Rivalry
                                        <p class="variable-details">How strong the competition is among existing players in the market.</p>
                                    </td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="1" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='1') ? 'checked' : '' ?>>Extremely fierce competition; very high market pressure.</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="2" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='2') ? 'checked' : '' ?>>Strong competition; notable market pressure.</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="3" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='3') ? 'checked' : '' ?>>Moderate competition; manageable market pressure.</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="4" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='4') ? 'checked' : '' ?>>Low competition; limited market pressure.</td>
                                    <td><input type="radio" name="intensity_of_rivalry" value="5" <?= (isset($_SESSION['business_form']['intensity_of_rivalry']) && $_SESSION['business_form']['intensity_of_rivalry']=='5') ? 'checked' : '' ?>>Very low competition; minimal market pressure.</td>
                                </tr>
                                <tr>
                                    <td>Porters 3: Substitution of Threat
                                        <p class="variable-details">The risk that customers switch to alternative products or services.</p>
                                    </td>
                                    <td><input type="radio" name="substitution_of_threat" value="1" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='1') ? 'checked' : '' ?>>Very high risk; customers easily switch to alternatives.</td>
                                    <td><input type="radio" name="substitution_of_threat" value="2" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='2') ? 'checked' : '' ?>>Above moderate risk; some likelihood of switching.</td>
                                    <td><input type="radio" name="substitution_of_threat" value="3" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='3') ? 'checked' : '' ?>>Moderate risk; switching is manageable.</td>
                                    <td><input type="radio" name="substitution_of_threat" value="4" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='4') ? 'checked' : '' ?>>Low risk; alternatives not very attractive.</td>
                                    <td><input type="radio" name="substitution_of_threat" value="5" <?= (isset($_SESSION['business_form']['substitution_of_threat']) && $_SESSION['business_form']['substitution_of_threat']=='5') ? 'checked' : '' ?>>Very low risk; customers unlikely to switch.</td>
                                </tr>
                                <tr>
                                    <td>Porters 4: Buyer Bargaining Power
                                        <p class="variable-details">How much influence customers have over prices and terms.</p>
                                    </td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="1" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='1') ? 'checked' : '' ?>>Buyers have a very strong influence; high pressure on prices/terms.</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="2" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='2') ? 'checked' : '' ?>>Buyers have moderate influence; noticeable pressure.</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="3" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='3') ? 'checked' : '' ?>>Buyers have manageable influence; acceptable pressure.</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="4" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='4') ? 'checked' : '' ?>>Buyers have limited influence; low pressure.</td>
                                    <td><input type="radio" name="buyer_bargaining_power" value="5" <?= (isset($_SESSION['business_form']['buyer_bargaining_power']) && $_SESSION['business_form']['buyer_bargaining_power']=='5') ? 'checked' : '' ?>>Buyers have minimal influence; very low pressure.</td>
                                </tr>
                                <tr>
                                    <td>Porters 5: Supplier Bargaining Power
                                        <p class="variable-details">How much influence suppliers have over costs and availability of inputs.</p>
                                    </td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="1" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='1') ? 'checked' : '' ?>>Suppliers have a very strong influence; high cost/availability risk.</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="2" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='2') ? 'checked' : '' ?>>Suppliers have moderate influence; noticeable impact.</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="3" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='3') ? 'checked' : '' ?>>Suppliers have manageable influence; acceptable risk.</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="4" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='4') ? 'checked' : '' ?>>Suppliers have limited influence; low impact.</td>
                                    <td><input type="radio" name="supplier_bargaining_power" value="5" <?= (isset($_SESSION['business_form']['supplier_bargaining_power']) && $_SESSION['business_form']['supplier_bargaining_power']=='5') ? 'checked' : '' ?>>Suppliers have minimal influence; very low impact.</td>
                                </tr>
                                <tr>
                                    <td>Overall Industry Outlook
                                        <p class="variable-details">How much influence suppliers have over costs and availability of inputs.</p>
                                    </td>
                                    <td><input type="radio" name="overall_industry_outlook" value="1" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='1') ? 'checked' : '' ?>>Industry declining or highly unstable; very challenging environment.</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="2" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='2') ? 'checked' : '' ?>>Industry growth is slow or somewhat unstable; notable challenges.</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="3" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='3') ? 'checked' : '' ?>>Industry stable with moderate growth; manageable environment.</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="4" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='4') ? 'checked' : '' ?>>Industry growing steadily; favorable environment.</td>
                                    <td><input type="radio" name="overall_industry_outlook" value="5" <?= (isset($_SESSION['business_form']['overall_industry_outlook']) && $_SESSION['business_form']['overall_industry_outlook']=='5') ? 'checked' : '' ?>>Industry growing strongly and stable; highly favorable environment.</td>
                                </tr>
                                <tr>
                                    <td>Market Position
                                        <p class="variable-details">The business’s current standing or competitiveness within its industry.</p>
                                    </td>
                                    <td><input type="radio" name="market_position" value="1" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='1') ? 'checked' : '' ?>>Weak position; very low competitiveness.</td>
                                    <td><input type="radio" name="market_position" value="2" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='2') ? 'checked' : '' ?>>Below average position; limited competitiveness.</td>
                                    <td><input type="radio" name="market_position" value="3" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='3') ? 'checked' : '' ?>>Average position; moderate competitiveness.</td>
                                    <td><input type="radio" name="market_position" value="4" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='4') ? 'checked' : '' ?>>Strong position; high competitiveness.</td>
                                    <td><input type="radio" name="market_position" value="5" <?= (isset($_SESSION['business_form']['market_position']) && $_SESSION['business_form']['market_position']=='5') ? 'checked' : '' ?>>Leading position; very strong competitiveness.</td>
                                </tr>
                        </table>
                        <div class="button-container">
                            <a href="business-form.php?step=2" class="next-button"><i class="fa-solid fa-caret-left"></i> Back</a>
                            <a href="business-form.php?step=4" class="next-button">Next <i class="fa-solid fa-caret-right"></i></a>
                        </div>
                    <?php endif; ?>