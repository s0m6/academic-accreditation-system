import re

with open('resources/views/requests/stageThreeForm.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Define the start and end of the block to replace
start_marker = "        <!-- Standards Content Area -->\n        <div id=\"standards-container\">"
end_marker = "        </div>\n      </div><!-- Section 3: Independent Evaluations -->"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

if start_idx == -1 or end_idx == -1:
    print("Markers not found!")
    exit(1)

# We want to replace everything between start_marker (inclusive of the div start) and end_marker (exclusive of the end tag of the container)
# Wait, let's just replace the interior of <div id="standards-container"> ... 
# Actually, replacing the whole block is easier.

new_block = """        <!-- Standards Content Area -->
        <div id="standards-container">
          <!-- Standard 1 -->
          <div id="standard-1-tab-content" class="std-content fade-in">
            <!-- Standard Header -->
            <div class="bg-slate-800 rounded-2xl p-6 shadow-xl mb-8 border-r-4 border-emerald-500">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400 font-bold text-xl">1</div>
                    المعيار الأول: الرسالة والأهداف
                  </h2>
                  <p class="text-slate-400 text-sm mt-2">يحتوي على 5 مؤشرات موزعة على المعايير الفرعية</p>
                </div>
                <div class="text-left bg-slate-900/50 p-4 rounded-xl border border-slate-700">
                  <span class="block text-xs text-slate-400 mb-1 text-center">التقييم</span>
                  <div class="flex items-center justify-center gap-1">
                      <span id="standard-1-score" class="text-3xl font-bold text-emerald-400">0.0</span>
                      <span class="text-slate-500 text-lg">/5</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Substandards Container -->
            <div class="space-y-8">
                
                <!-- Substandard 1.1 -->
                <div class="bg-slate-800/80 rounded-2xl shadow-lg border border-slate-700/50 overflow-hidden">
                    <div class="bg-slate-700/30 p-4 border-b border-slate-700/50 flex items-center gap-3">
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg font-bold text-sm">1.1</span>
                        <h3 class="font-bold text-lg text-white">رسالة البرنامج</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Indicator 1-1 -->
                        <div class="indicator-row rounded-xl p-5 border border-slate-600 bg-slate-800/50" data-indicator="1-1">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-1</span>
                                    <p class="text-white mt-1">وضوح رسالة البرنامج واتساقها مع رسالة المؤسسة</p>
                                </div>
                                <div class="tooltip-container mr-2">
                                    <svg class="w-5 h-5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="tooltip-text"><strong>أمثلة للأدلة:</strong><br>• وثيقة الرسالة المعتمدة<br>• محاضر اجتماعات المراجعة<br>• استبيانات أصحاب المصلحة</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                                <div class="flex gap-2">
                                    <button onclick="setRating(1, 1, 1)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500" data-rating="1">1</button>
                                    <button onclick="setRating(1, 1, 2)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500" data-rating="2">2</button>
                                    <button onclick="setRating(1, 1, 3)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500" data-rating="3">3</button>
                                    <button onclick="setRating(1, 1, 4)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500" data-rating="4">4</button>
                                    <button onclick="setRating(1, 1, 5)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500" data-rating="5">5</button>
                                </div>
                            </div>
                            <div id="evidences-1-1" class="space-y-2 mb-3"></div>
                            <button onclick="addEvidence(1, 1)" class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> إرفاق دليل
                            </button>
                        </div>
                        
                        <!-- Indicator 1-2 -->
                        <div class="indicator-row rounded-xl p-5 border border-slate-600 bg-slate-800/50" data-indicator="1-2">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-2</span>
                                    <p class="text-white mt-1">مشاركة أصحاب المصلحة في صياغة الرسالة والأهداف</p>
                                </div>
                                <div class="tooltip-container mr-2">
                                    <svg class="w-5 h-5 text-slate-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="tooltip-text"><strong>أمثلة للأدلة:</strong><br>• محاضر الاجتماعات<br>• قوائم الحضور<br>• نماذج الاستبيانات</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                                <div class="flex gap-2">
                                    <button onclick="setRating(1, 2, 1)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500" data-rating="1">1</button>
                                    <button onclick="setRating(1, 2, 2)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500" data-rating="2">2</button>
                                    <button onclick="setRating(1, 2, 3)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500" data-rating="3">3</button>
                                    <button onclick="setRating(1, 2, 4)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500" data-rating="4">4</button>
                                    <button onclick="setRating(1, 2, 5)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500" data-rating="5">5</button>
                                </div>
                            </div>
                            <div id="evidences-1-2" class="space-y-2 mb-3"></div>
                            <button onclick="addEvidence(1, 2)" class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> إرفاق دليل
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Substandard 1.2 -->
                <div class="bg-slate-800/80 rounded-2xl shadow-lg border border-slate-700/50 overflow-hidden">
                    <div class="bg-slate-700/30 p-4 border-b border-slate-700/50 flex items-center gap-3">
                        <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg font-bold text-sm">1.2</span>
                        <h3 class="font-bold text-lg text-white">أهداف البرنامج</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <!-- Indicator 1-3 -->
                        <div class="indicator-row rounded-xl p-5 border border-slate-600 bg-slate-800/50" data-indicator="1-3">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-3</span>
                                    <p class="text-white mt-1">قابلية قياس أهداف البرنامج</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                                <div class="flex gap-2">
                                    <button onclick="setRating(1, 3, 1)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500" data-rating="1">1</button>
                                    <button onclick="setRating(1, 3, 2)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500" data-rating="2">2</button>
                                    <button onclick="setRating(1, 3, 3)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500" data-rating="3">3</button>
                                    <button onclick="setRating(1, 3, 4)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500" data-rating="4">4</button>
                                    <button onclick="setRating(1, 3, 5)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500" data-rating="5">5</button>
                                </div>
                            </div>
                            <div id="evidences-1-3" class="space-y-2 mb-3"></div>
                            <button onclick="addEvidence(1, 3)" class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> إرفاق دليل
                            </button>
                        </div>
                        
                        <!-- Indicator 1-4 -->
                        <div class="indicator-row rounded-xl p-5 border border-slate-600 bg-slate-800/50" data-indicator="1-4">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-4</span>
                                    <p class="text-white mt-1">توافق أهداف البرنامج مع رسالته</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                                <div class="flex gap-2">
                                    <button onclick="setRating(1, 4, 1)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500" data-rating="1">1</button>
                                    <button onclick="setRating(1, 4, 2)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500" data-rating="2">2</button>
                                    <button onclick="setRating(1, 4, 3)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500" data-rating="3">3</button>
                                    <button onclick="setRating(1, 4, 4)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500" data-rating="4">4</button>
                                    <button onclick="setRating(1, 4, 5)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500" data-rating="5">5</button>
                                </div>
                            </div>
                            <div id="evidences-1-4" class="space-y-2 mb-3"></div>
                            <button onclick="addEvidence(1, 4)" class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> إرفاق دليل
                            </button>
                        </div>
                        
                        <!-- Indicator 1-5 -->
                        <div class="indicator-row rounded-xl p-5 border border-slate-600 bg-slate-800/50" data-indicator="1-5">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1"><span class="text-xs text-blue-400 font-medium">مؤشر 1-5</span>
                                    <p class="text-white mt-1">آلية المراجعة الدورية للرسالة والأهداف</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mb-4"><span class="text-sm text-slate-400">التقييم:</span>
                                <div class="flex gap-2">
                                    <button onclick="setRating(1, 5, 1)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-red-500" data-rating="1">1</button>
                                    <button onclick="setRating(1, 5, 2)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-orange-500" data-rating="2">2</button>
                                    <button onclick="setRating(1, 5, 3)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-yellow-500" data-rating="3">3</button>
                                    <button onclick="setRating(1, 5, 4)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-lime-500" data-rating="4">4</button>
                                    <button onclick="setRating(1, 5, 5)" class="rating-btn w-10 h-10 rounded-lg bg-slate-600 text-white font-bold hover:bg-emerald-500" data-rating="5">5</button>
                                </div>
                            </div>
                            <div id="evidences-1-5" class="space-y-2 mb-3"></div>
                            <button onclick="addEvidence(1, 5)" class="text-blue-400 text-sm flex items-center gap-2 hover:text-blue-300 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> إرفاق دليل
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Standard Comments -->
            <div class="mt-8 bg-slate-800 rounded-2xl shadow-xl overflow-hidden border border-slate-700">
                <div class="bg-slate-700/50 p-4 border-b border-slate-600">
                    <h4 class="font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg> التعليقات الختامية للمعيار
                    </h4>
                </div>
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">تعليق البرنامج</label> 
                        <textarea rows="3" placeholder="أدخل تعليق البرنامج على هذا المعيار..." class="w-full px-4 py-3 rounded-xl resize-none bg-slate-900 border border-slate-600 focus:ring-2 focus:ring-blue-500 text-white" onchange="saveStandardComment(1, 'program_comment', this.value)"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-emerald-400 mb-2">جوانب القوة</label> 
                        <textarea rows="3" placeholder="• نقطة قوة 1" class="w-full px-4 py-3 rounded-xl resize-none bg-emerald-900/10 border border-emerald-800/50 focus:ring-2 focus:ring-emerald-500 text-white" onchange="saveStandardComment(1, 'strengths', this.value)"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-red-400 mb-2">جوانب تحتاج تحسين</label> 
                        <textarea rows="3" placeholder="• جانب للتحسين 1" class="w-full px-4 py-3 rounded-xl resize-none bg-red-900/10 border border-red-800/50 focus:ring-2 focus:ring-red-500 text-white" onchange="saveStandardComment(1, 'improvements', this.value)"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-amber-400 mb-2">أولويات التحسين</label> 
                        <textarea rows="3" placeholder="• أولوية 1" class="w-full px-4 py-3 rounded-xl resize-none bg-amber-900/10 border border-amber-800/50 focus:ring-2 focus:ring-amber-500 text-white" onchange="saveStandardComment(1, 'priorities', this.value)"></textarea>
                    </div>
                    <div class="pt-4 border-t border-slate-700">
                        <label class="block text-sm font-medium text-blue-400 mb-2">الرأي المستقل</label> 
                        <textarea rows="3" placeholder="أدخل الرأي المستقل..." class="w-full px-4 py-3 rounded-xl resize-none bg-blue-900/10 border border-blue-800/50 focus:ring-2 focus:ring-blue-500 text-white" onchange="saveStandardComment(1, 'independent_opinion', this.value)"></textarea>
                    </div>
                </div>
            </div>
          </div>
          
          <!-- Standard 2 Placeholder -->
          <div id="standard-2-tab-content" class="std-content hidden fade-in">
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl text-center border-t-4 border-emerald-500">
                  <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                      <span class="text-3xl font-bold text-emerald-400">2</span>
                  </div>
                  <h3 class="text-xl font-bold text-white mb-2">إدارة البرنامج وضمان الجودة</h3>
                  <p class="text-slate-400">قريباً.. نفس البنية سيتم تطبيقها هنا مع 6 مؤشرات</p>
              </div>
          </div>

          <!-- Standard 3 Placeholder -->
          <div id="standard-3-tab-content" class="std-content hidden fade-in">
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl text-center border-t-4 border-emerald-500">
                  <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                      <span class="text-3xl font-bold text-emerald-400">3</span>
                  </div>
                  <h3 class="text-xl font-bold text-white mb-2">التعليم والتعلم</h3>
                  <p class="text-slate-400">قريباً.. نفس البنية سيتم تطبيقها هنا مع 8 مؤشرات</p>
              </div>
          </div>

          <!-- Standard 4 Placeholder -->
          <div id="standard-4-tab-content" class="std-content hidden fade-in">
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl text-center border-t-4 border-emerald-500">
                  <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                      <span class="text-3xl font-bold text-emerald-400">4</span>
                  </div>
                  <h3 class="text-xl font-bold text-white mb-2">الطلاب</h3>
                  <p class="text-slate-400">قريباً.. نفس البنية سيتم تطبيقها هنا مع 7 مؤشرات</p>
              </div>
          </div>

          <!-- Standard 5 Placeholder -->
          <div id="standard-5-tab-content" class="std-content hidden fade-in">
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl text-center border-t-4 border-emerald-500">
                  <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                      <span class="text-3xl font-bold text-emerald-400">5</span>
                  </div>
                  <h3 class="text-xl font-bold text-white mb-2">هيئة التدريس</h3>
                  <p class="text-slate-400">قريباً.. نفس البنية سيتم تطبيقها هنا مع 6 مؤشرات</p>
              </div>
          </div>

          <!-- Standard 6 Placeholder -->
          <div id="standard-6-tab-content" class="std-content hidden fade-in">
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl text-center border-t-4 border-emerald-500">
                  <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                      <span class="text-3xl font-bold text-emerald-400">6</span>
                  </div>
                  <h3 class="text-xl font-bold text-white mb-2">مصادر التعلم والمرافق</h3>
                  <p class="text-slate-400">قريباً.. نفس البنية سيتم تطبيقها هنا مع 5 مؤشرات</p>
              </div>
          </div>

          <!-- Standard 7 Placeholder -->
          <div id="standard-7-tab-content" class="std-content hidden fade-in">
              <div class="bg-slate-800 rounded-2xl p-6 shadow-xl text-center border-t-4 border-emerald-500">
                  <div class="w-16 h-16 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                      <span class="text-3xl font-bold text-emerald-400">7</span>
                  </div>
                  <h3 class="text-xl font-bold text-white mb-2">البحث العلمي والابتكار</h3>
                  <p class="text-slate-400">قريباً.. نفس البنية سيتم تطبيقها هنا مع 4 مؤشرات</p>
              </div>
          </div>"""

# Ensure new block fits cleanly
new_content = content[:start_idx] + new_block + content[end_idx:]

with open('resources/views/requests/stageThreeForm.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Replaced section successfully!")

