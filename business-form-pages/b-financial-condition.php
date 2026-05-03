<?php if ($currentStep == 2): ?>
                    <h3>Step 2 – Financial Condition</h3>
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
                                <td>Capital to Risk Assets Ratio (%)
                                    <p class="variable-details">How well the business’s capital covers its financial risks.</p>
                                </td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="1" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='1') ? 'checked' : '' ?>>Well below minimum; high risk.</td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="2" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='2') ? 'checked' : '' ?>>Slightly below minimum; insufficient buffer.</td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="3" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='3') ? 'checked' : '' ?>>Meets minimum requirements; adequate capital.</td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="4" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='4') ? 'checked' : '' ?>>Exceeds minimum; comfortable buffer.</td>
                                <td><input type="radio" name="capital_to_risk_assets_ratio" value="5" <?= (isset($_SESSION['business_form']['capital_to_risk_assets_ratio']) && $_SESSION['business_form']['capital_to_risk_assets_ratio']=='5') ? 'checked' : '' ?>>Well above minimum; very strong capitalization.</td>
                            </tr>


                            <tr>
                                <td>Debt-to-Equity Ratio (X)
                                    <p class="variable-details">How much debt the business has compared to its own equity.</p>
                                </td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="1" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='1') ? 'checked' : '' ?>>Debt far exceeds equity; very high financial risk.</td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="2" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='2') ? 'checked' : '' ?>>Debt somewhat exceeds equity; elevated risk.</td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="3" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='3') ? 'checked' : '' ?>>Debt roughly equals equity; moderate risk.</td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="4" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='4') ? 'checked' : '' ?>>Debt is lower than equity; manageable risk.</td>
                                <td><input type="radio" name="debt_to_equity_ratio" value="5" <?= (isset($_SESSION['business_form']['debt_to_equity_ratio']) && $_SESSION['business_form']['debt_to_equity_ratio']=='5') ? 'checked' : '' ?>>Debt is well below equity; very low financial risk.</td>
                            </tr>


                            <tr>
                                <td>NPL Ratio (Non-Performing Loan Ratio)
                                    <p class="variable-details">The share of the business’s loans that are overdue or not being repaid on time.</p>
                                </td>
                                <td><input type="radio" name="npl_ratio" value="1" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='1') ? 'checked' : '' ?>>Very high proportion of overdue loans; critical risk.</td>
                                <td><input type="radio" name="npl_ratio" value="2" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='2') ? 'checked' : '' ?>>Above acceptable levels; notable repayment issues.</td>
                                <td><input type="radio" name="npl_ratio" value="3" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='3') ? 'checked' : '' ?>>Within acceptable limits; manageable risk.</td>
                                <td><input type="radio" name="npl_ratio" value="4" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='4') ? 'checked' : '' ?>>Low proportion of overdue loans; minimal risk.</td>
                                <td><input type="radio" name="npl_ratio" value="5" <?= (isset($_SESSION['business_form']['npl_ratio']) && $_SESSION['business_form']['npl_ratio']=='5') ? 'checked' : '' ?>>Very low or no overdue loans; excellent repayment performance.</td>
                            </tr>


                            <tr>
                                <td>NPA Ratio (Non-Performing Assets Ratio)
                                    <p class="variable-details">The proportion of the business’s assets that are not generating expected returns due to payment defaults.</p>
                                </td>
                                <td><input type="radio" name="npa_ratio" value="1" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='1') ? 'checked' : '' ?>>Very high proportion of non-performing assets; critical financial concern.</td>
                                <td><input type="radio" name="npa_ratio" value="2" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='2') ? 'checked' : '' ?>>Above acceptable levels; notable asset quality issues.</td>
                                <td><input type="radio" name="npa_ratio" value="3" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='3') ? 'checked' : '' ?>>Within acceptable limits; manageable asset risk.</td>
                                <td><input type="radio" name="npa_ratio" value="4" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='4') ? 'checked' : '' ?>>Low proportion of non-performing assets; strong asset quality.</td>
                                <td><input type="radio" name="npa_ratio" value="5" <?= (isset($_SESSION['business_form']['npa_ratio']) && $_SESSION['business_form']['npa_ratio']=='5') ? 'checked' : '' ?>>Very low or no non-performing assets; excellent asset performance.</td>
                            </tr>


                            <tr>
                                <td>NPA Coverage Ratio
                                    <p class="variable-details">How much of the business’s non-performing assets are covered by provisions or reserves.</p>
                                </td>
                                <td><input type="radio" name="npa_coverage_ratio" value="1" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='1') ? 'checked' : '' ?>>Very low coverage; high exposure to losses.</td>
                                <td><input type="radio" name="npa_coverage_ratio" value="2" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='2') ? 'checked' : '' ?>>Coverage below recommended levels; risk of loss remains.</td>
                                <td><input type="radio" name="npa_coverage_ratio" value="3" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='3') ? 'checked' : '' ?>>Adequate coverage; potential losses manageable.</td>
                                <td><input type="radio" name="npa_coverage_ratio" value="4" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='4') ? 'checked' : '' ?>>Coverage exceeds minimum; strong protection against losses.</td>
                                <td><input type="radio" name="npa_coverage_ratio" value="5" <?= (isset($_SESSION['business_form']['npa_coverage_ratio']) && $_SESSION['business_form']['npa_coverage_ratio']=='5') ? 'checked' : '' ?>>Very high coverage; non-performing assets well secured.</td>
                            </tr>
                            <tr>
                                <td>ROAE (Return on Average Equity)
                                    <p class="variable-details">How much profit the business earns compared to its equity.</p>
                                </td>
                                <td><input type="radio" name="roae" value="1" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='1') ? 'checked' : '' ?>>Very low or negative return; equity not generating profit.</td>
                                <td><input type="radio" name="roae" value="2" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='2') ? 'checked' : '' ?>>Return below expectations; weak profitability.</td>
                                <td><input type="radio" name="roae" value="3" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='3') ? 'checked' : '' ?>>Meets expected return; moderate profitability.</td>
                                <td><input type="radio" name="roae" value="4" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='4') ? 'checked' : '' ?>>Exceeds expectations; strong profitability.</td>
                                <td><input type="radio" name="roae" value="5" <?= (isset($_SESSION['business_form']['roae']) && $_SESSION['business_form']['roae']=='5') ? 'checked' : '' ?>>Significantly above expectations; outstanding profitability.</td>
                            </tr>


                            <tr>
                                <td>ROAA (Return on Average Assets)
                                    <p class="variable-details">How much profit the business earns compared to its total assets.</p>
                                </td>
                                <td><input type="radio" name="roaa" value="1" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='1') ? 'checked' : '' ?>>Very low or negative return; assets underperforming.</td>
                                <td><input type="radio" name="roaa" value="2" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='2') ? 'checked' : '' ?>>Return below expectations; weak asset efficiency.</td>
                                <td><input type="radio" name="roaa" value="3" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='3') ? 'checked' : '' ?>>Meets expected return; moderate asset performance.</td>
                                <td><input type="radio" name="roaa" value="4" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='4') ? 'checked' : '' ?>>Exceeds expectations; strong asset utilization.</td>
                                <td><input type="radio" name="roaa" value="5" <?= (isset($_SESSION['business_form']['roaa']) && $_SESSION['business_form']['roaa']=='5') ? 'checked' : '' ?>>Significantly above expectations; outstanding asset efficiency.</td>
                            </tr>


                            <tr>
                                <td>Cost to Income Ratio
                                    <p class="variable-details">How much of the business’s income is spent on operating costs.</p>
                                </td>
                                <td><input type="radio" name="cost_to_income_ratio" value="1" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='1') ? 'checked' : '' ?>>Very high costs relative to income; inefficient operations.</td>
                                <td><input type="radio" name="cost_to_income_ratio" value="2" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='2') ? 'checked' : '' ?>>Costs somewhat high; below optimal efficiency.</td>
                                <td><input type="radio" name="cost_to_income_ratio" value="3" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='3') ? 'checked' : '' ?>>Costs in line with income; acceptable efficiency.</td>
                                <td><input type="radio" name="cost_to_income_ratio" value="4" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='4') ? 'checked' : '' ?>>Costs lower than income; efficient operations.</td>
                                <td><input type="radio" name="cost_to_income_ratio" value="5" <?= (isset($_SESSION['business_form']['cost_to_income_ratio']) && $_SESSION['business_form']['cost_to_income_ratio']=='5') ? 'checked' : '' ?>>Very low costs relative to income; highly efficient operations.</td>
                            </tr>


                            <tr>
                                <td>Liquid Assets to Borrowed Funds
                                    <p class="variable-details">How easily the business can cover its borrowed funds with its liquid assets.</p>
                                </td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="1" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='1') ? 'checked' : '' ?>>Very low liquidity; cannot cover borrowed funds.</td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="2" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='2') ? 'checked' : '' ?>>Liquidity below adequate levels; potential repayment risk.</td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="3" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='3') ? 'checked' : '' ?>>Liquidity sufficient to cover borrowed funds; manageable risk.</td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="4" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='4') ? 'checked' : '' ?>>Liquidity exceeds borrowed funds; low repayment risk.</td>
                                <td><input type="radio" name="liquid_assets_to_borrowed_funds" value="5" <?= (isset($_SESSION['business_form']['liquid_assets_to_borrowed_funds']) && $_SESSION['business_form']['liquid_assets_to_borrowed_funds']=='5') ? 'checked' : '' ?>>Very high liquidity relative to borrowed funds; strong financial safety.</td>
                            </tr>


                            <tr>
                                <td>Debt Service Cover (X)
                                    <p class="variable-details">How easily the business can pay interest and principal on its debt from its cash flow.</p>
                                </td>
                                <td><input type="radio" name="debt_service_cover" value="1" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='1') ? 'checked' : '' ?>>Cash flow insufficient to cover debt; high default risk.</td>
                                <td><input type="radio" name="debt_service_cover" value="2" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='2') ? 'checked' : '' ?>>Cash flow barely covers debt; elevated repayment risk.</td>
                                <td><input type="radio" name="debt_service_cover" value="3" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='3') ? 'checked' : '' ?>>Cash flow covers debt; manageable repayment risk.</td>
                                <td><input type="radio" name="debt_service_cover" value="4" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='4') ? 'checked' : '' ?>>Cash flow comfortably covers debt; low repayment risk.</td>
                                <td><input type="radio" name="debt_service_cover" value="5" <?= (isset($_SESSION['business_form']['debt_service_cover']) && $_SESSION['business_form']['debt_service_cover']=='5') ? 'checked' : '' ?>>Cash flow far exceeds debt obligations; very strong repayment capacity.</td>
                            </tr>
                        </tbody>
                    </table>


                    <div class="button-container">
                        <a href="business-form.php?step=1" class="next-button"><i class="fa-solid fa-caret-left"></i> Back</a>
                        <a href="business-form.php?step=3" class="next-button">Next <i class="fa-solid fa-caret-right"></i></a>
                    </div>
                    <?php endif; ?>