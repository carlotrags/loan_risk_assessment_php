<?php if ($currentStep == 4): ?>
                    <h3>Step 4 – Management Quality</h3>
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
                                    <td>Character of Management
                                        <p class="variable-details">The integrity and trustworthiness of the business’s leadership.</p>
                                    </td>
                                    <td><input type="radio" name="character_of_management" value="1" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='1') ? 'checked' : '' ?>>Low integrity; untrustworthy leadership.</td>
                                    <td><input type="radio" name="character_of_management" value="2" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='2') ? 'checked' : '' ?>>Some concerns about integrity; inconsistent trustworthiness.</td>
                                    <td><input type="radio" name="character_of_management" value="3" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='3') ? 'checked' : '' ?>>Generally trustworthy; acceptable integrity.</td>
                                    <td><input type="radio" name="character_of_management" value="4" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='4') ? 'checked' : '' ?>>High integrity; reliable leadership.</td>
                                    <td><input type="radio" name="character_of_management" value="5" <?= (isset($_SESSION['business_form']['character_of_management']) && $_SESSION['business_form']['character_of_management']=='5') ? 'checked' : '' ?>>Very high integrity; exceptionally trustworthy leadership.</td>
                                </tr>
                                <tr>
                                    <td>Quality and Experience of Management
                                        <p class="variable-details">How skilled and experienced the business’s management team is.</p>
                                    </td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="1" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='1') ? 'checked' : '' ?>>Management lacks skills and experience; weak leadership.</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="2" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='2') ? 'checked' : '' ?>>Limited skills or experience; some capability gaps.</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="3" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='3') ? 'checked' : '' ?>>Competent management; adequate skills and experience.</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="4" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='4') ? 'checked' : '' ?>>Skilled and experienced management; strong leadership.</td>
                                    <td><input type="radio" name="quality_and_experience_of_management" value="5" <?= (isset($_SESSION['business_form']['quality_and_experience_of_management']) && $_SESSION['business_form']['quality_and_experience_of_management']=='5') ? 'checked' : '' ?>>Highly skilled and experienced; exceptional leadership.</td>
                                </tr>
                                <tr>
                                    <td>Bank Relationship
                                        <p class="variable-details">The business’s history and quality of interactions with this bank, including past loans, repayments, and overall trustworthiness.</p>
                                    </td>
                                    <td><input type="radio" name="bank_relationship" value="1" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='1') ? 'checked' : '' ?>>Weak or problematic interactions; low trust.</td>
                                    <td><input type="radio" name="bank_relationship" value="2" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='2') ? 'checked' : '' ?>>Some concerns or inconsistencies; moderate reliability.</td>
                                    <td><input type="radio" name="bank_relationship" value="3" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='3') ? 'checked' : '' ?>>Stable relationship; acceptable trust and reliability.</td>
                                    <td><input type="radio" name="bank_relationship" value="4" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='4') ? 'checked' : '' ?>>Strong and reliable relationship; high trust.</td>
                                    <td><input type="radio" name="bank_relationship" value="5" <?= (isset($_SESSION['business_form']['bank_relationship']) && $_SESSION['business_form']['bank_relationship']=='5') ? 'checked' : '' ?>>Very strong and trustworthy relationship; exemplary record.</td>
                                </tr>
                                <tr>
                                    <td>Labor Relations
                                        <p class="variable-details">How well the business manages its employees and labor issues.</p>
                                    </td>
                                    <td><input type="radio" name="labor_relations" value="1" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='1') ? 'checked' : '' ?>>Frequent labor issues; poor employee management.</td>
                                    <td><input type="radio" name="labor_relations" value="2" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='2') ? 'checked' : '' ?>>Occasional issues; some management challenges.</td>
                                    <td><input type="radio" name="labor_relations" value="3" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='3') ? 'checked' : '' ?>>Generally good; manageable labor relations.</td>
                                    <td><input type="radio" name="labor_relations" value="4" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='4') ? 'checked' : '' ?>>Positive relations; effective employee management.</td>
                                    <td><input type="radio" name="labor_relations" value="5" <?= (isset($_SESSION['business_form']['labor_relations']) && $_SESSION['business_form']['labor_relations']=='5') ? 'checked' : '' ?>>Excellent labor relations; highly effective management.</td>
                                </tr>
                                <tr>
                                    <td>Existence
                                        <p class="variable-details">How long the business has been operating.</p>
                                    </td>
                                    <td><input type="radio" name="existence" value="1" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='1') ? 'checked' : '' ?>>Very new; minimal operating history.</td>
                                    <td><input type="radio" name="existence" value="2" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='2') ? 'checked' : '' ?>>Short operating history; limited track record.</td>
                                    <td><input type="radio" name="existence" value="3" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='3') ? 'checked' : '' ?>>Moderate history; adequate track record.</td>
                                    <td><input type="radio" name="existence" value="4" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='4') ? 'checked' : '' ?>>Long-standing; strong operating history.</td>
                                    <td><input type="radio" name="existence" value="5" <?= (isset($_SESSION['business_form']['existence']) && $_SESSION['business_form']['existence']=='5') ? 'checked' : '' ?>>Very long-established; highly stable and proven</td>
                                </tr>
                                <tr>
                                    <td>NFIS/CMAP Checkings
                                        <p class="variable-details">(Bad Record/Credit Investigation) Any past negative records or credit issues identified in official checks.</p>
                                    </td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="1" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='1') ? 'checked' : '' ?>>Multiple negative records; high credit risk.</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="2" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='2') ? 'checked' : '' ?>>Some negative records; moderate credit risk.</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="3" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='3') ? 'checked' : '' ?>>Minor issues; manageable credit risk.</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="4" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='4') ? 'checked' : '' ?>>Clean record; low credit risk.</td>
                                    <td><input type="radio" name="nfis_cmap_checkings" value="5" <?= (isset($_SESSION['business_form']['nfis_cmap_checkings']) && $_SESSION['business_form']['nfis_cmap_checkings']=='5') ? 'checked' : '' ?>>Very clean record; minimal or no credit risk.</td>
                                </tr>
                                <tr>
                                    <td>Management Control and Business Planning
                                        <p class="variable-details">How effectively management plans and controls business operations.</p>
                                    </td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="1" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='1') ? 'checked' : '' ?>>Weak planning and control; high operational risk.</td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="2" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='2') ? 'checked' : '' ?>>Inconsistent planning and control; moderate risk.</td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="3" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='3') ? 'checked' : '' ?>>Adequate planning and control; manageable risk.</td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="4" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='4') ? 'checked' : '' ?>>Strong planning and control; low operational risk./td>
                                    <td><input type="radio" name="management_cntrl_business_planning" value="5" <?= (isset($_SESSION['business_form']['management_cntrl_business_planning']) && $_SESSION['business_form']['management_cntrl_business_planning']=='5') ? 'checked' : '' ?>>Very effective planning and control; minimal operational risk.</td>
                                </tr>
                                <tr>
                                    <td>Management Structure and Successtion Strategy
                                        <p class="variable-details">How organized the management team is and whether there’s a clear succession plan.</p>
                                    </td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="1" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='1') ? 'checked' : '' ?>>Disorganized structure; no succession plan.</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="2" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='2') ? 'checked' : '' ?>>Some structure; limited succession planning.</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="3" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='3') ? 'checked' : '' ?>>Adequate structure; basic succession plan.</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="4" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='4') ? 'checked' : '' ?>>Well-organized structure; clear succession strategy.</td>
                                    <td><input type="radio" name="management_structure_succession_strategy" value="5" <?= (isset($_SESSION['business_form']['management_structure_succession_strategy']) && $_SESSION['business_form']['management_structure_succession_strategy']=='5') ? 'checked' : '' ?>>Highly structured; robust succession planning.</td>
                                </tr>
                                <tr>
                                    <td>Clear Long-Term Management Strategy
                                        <p class="variable-details">Whether the business has a well-defined plan for long-term growth and sustainability.</p>
                                    </td>
                                    <td><input type="radio" name="long_term_management_strategy" value="1" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='1') ? 'checked' : '' ?>>No long-term strategy; unclear growth direction.</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="2" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='2') ? 'checked' : '' ?>>Limited strategy; vague long-term plans.</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="3" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='3') ? 'checked' : '' ?>>Basic strategy; acceptable long-term planning.</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="4" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='4') ? 'checked' : '' ?>>Clear strategy; strong long-term planning.</td>
                                    <td><input type="radio" name="long_term_management_strategy" value="5" <?= (isset($_SESSION['business_form']['long_term_management_strategy']) && $_SESSION['business_form']['long_term_management_strategy']=='5') ? 'checked' : '' ?>>Well-defined and robust strategy; excellent long-term vision.</td>
                                </tr>
                            </table>


                            <div class="button-container">
                                <a href="business-form.php?step=3" class="next-button"><i class="fa-solid fa-caret-left"></i> Back</a>
                                <a href="business-form.php?step=5" class="next-button">Next <i class="fa-solid fa-caret-right"></i></a>
                            </div>
                        <?php endif; ?>